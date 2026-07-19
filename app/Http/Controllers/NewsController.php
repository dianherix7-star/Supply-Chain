<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\News;
use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    /**
     * Tampilkan daftar berita beserta sentiment statistics
     */
    public function index(Request $request)
    {
        $query = News::with('country')->latest('published_at');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $news = $query->paginate(15)->withQueryString();

        $total    = News::count();
        $positive = News::where('sentiment', 'Positive')->count();
        $negative = News::where('sentiment', 'Negative')->count();
        $neutral  = News::where('sentiment', 'Neutral')->count();

        $stats = [
            'total'    => $total,
            'positive' => $positive,
            'neutral'  => $neutral,
            'negative' => $negative,
            'pos_pct'  => $total > 0 ? round(($positive / $total) * 100, 1) : 0,
            'neu_pct'  => $total > 0 ? round(($neutral  / $total) * 100, 1) : 0,
            'neg_pct'  => $total > 0 ? round(($negative / $total) * 100, 1) : 0,
        ];

        $countries = Country::orderBy('country_name')->get(['id', 'country_name']);

        return view('news.index', compact('news', 'stats', 'countries'));
    }

    /**
     * ============================================================
     * Fetch berita untuk SATU negara via GNews API (Sumber Utama)
     * GNews: https://gnews.io — 10 req/hari free tier
     * ============================================================
     */
    public function fetchByCountry(Country $country)
    {
        try {
            $gnewsKey = config('services.gnews.key');

            // Prioritas 1: GNews API (sesuai spesifikasi project)
            if (!empty($gnewsKey)) {
                return $this->fetchFromGNews($country, $gnewsKey);
            }

            // Prioritas 2: NewsAPI.org sebagai fallback
            $newsApiKey = config('services.news_api.key');
            if (!empty($newsApiKey)) {
                return $this->fetchFromNewsApi($country, $newsApiKey);
            }

            return back()->with('error', 'API Key berita belum dikonfigurasi. Set GNEWS_API_KEY di file .env');

        } catch (\Exception $e) {
            Log::error('NewsController@fetchByCountry error: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Fetch berita untuk SEMUA negara sekaligus
     * Batasi 10 negara saja agar tidak habis rate limit GNews (10 req/hari)
     */
    public function fetchAll()
    {
        $gnewsKey   = config('services.gnews.key');
        $newsApiKey = config('services.news_api.key');

        if (empty($gnewsKey) && empty($newsApiKey)) {
            return back()->with('error', 'API Key berita belum diatur. Set GNEWS_API_KEY di file .env');
        }

        // Ambil negara penting untuk supply chain global
        $priorityCountries = [
            'CN', 'US', 'DE', 'JP', 'ID', 'SG', 'IN', 'AU', 'GB', 'BR'
        ];

        $countries = Country::whereIn('country_code', $priorityCountries)
            ->orderBy('country_name')
            ->get();

        if ($countries->isEmpty()) {
            $countries = Country::whereNotNull('country_name')->limit(10)->get();
        }

        $success = 0;
        $failed  = 0;
        $total   = 0;

        foreach ($countries as $country) {
            try {
                $imported = 0;
                if (!empty($gnewsKey)) {
                    $imported = $this->fetchFromGNews($country, $gnewsKey, false);
                } elseif (!empty($newsApiKey)) {
                    $imported = $this->fetchFromNewsApi($country, $newsApiKey, false);
                }
                $total += $imported;
                $success++;
                // Jeda kecil agar tidak kena rate limit
                usleep(300000); // 300ms
            } catch (\Exception $e) {
                Log::warning("Fetch news failed for {$country->country_name}: " . $e->getMessage());
                $failed++;
            }
        }

        return back()->with('success',
            "✅ Fetch selesai! {$success} negara berhasil ({$total} berita), {$failed} gagal. "
            . "Sumber: " . (!empty($gnewsKey) ? 'GNews API' : 'NewsAPI.org')
        );
    }

    /**
     * ============================================================
     * FETCH DARI GNEWS API — sumber utama sesuai spesifikasi
     * Endpoint: https://gnews.io/api/v4/search
     * Parameter: q (query), token (api key), lang, max
     * ============================================================
     *
     * @param bool $redirect  true = return redirect, false = return int (count)
     */
    private function fetchFromGNews(Country $country, string $apiKey, bool $redirect = true): mixed
    {
        // Query keyword: nama negara + topik supply chain
        $query = "\"{$country->country_name}\" (economy OR trade OR logistics OR shipping OR supply chain OR inflation OR port)";

        $response = Http::timeout(20)->get('https://gnews.io/api/v4/search', [
            'q'      => $query,
            'token'  => $apiKey,
            'lang'   => 'en',
            'max'    => 10,        // max 10 per request
            'sortby' => 'publishedAt',
        ]);

        if (!$response->successful()) {
            $err = "GNews API error [{$response->status()}] untuk {$country->country_name}";
            if ($redirect) return back()->with('error', $err);
            throw new \Exception($err);
        }

        $json     = $response->json();
        $articles = $json['articles'] ?? [];

        if (empty($articles)) {
            if ($redirect) {
                return back()->with('info',
                    "Tidak ada artikel untuk {$country->country_name} di GNews. "
                    . "Total permintaan sisa: " . ($json['totalArticles'] ?? '?')
                );
            }
            return 0;
        }

        $imported = 0;
        foreach ($articles as $article) {
            $title = trim($article['title'] ?? '');
            $desc  = trim($article['description'] ?? $article['content'] ?? '');

            if (!$title) continue;

            // Analisis sentimen lexicon-based
            $sentiment = $this->analyzeSentiment($title . ' ' . $desc);

            News::updateOrCreate(
                [
                    'country_id' => $country->id,
                    'title'      => $title,
                ],
                [
                    'description'  => $desc,
                    'url'          => $article['url'] ?? null,
                    'source'       => $article['source']['name'] ?? 'GNews',
                    'sentiment'    => $sentiment,
                    'published_at' => isset($article['publishedAt'])
                        ? \Carbon\Carbon::parse($article['publishedAt'])
                        : now(),
                ]
            );
            $imported++;
        }

        if ($redirect) {
            return back()->with('success',
                "✅ GNews: {$imported} berita berhasil diimpor untuk {$country->country_name}."
            );
        }

        return $imported;
    }

    /**
     * Fetch dari NewsAPI.org (fallback)
     */
    private function fetchFromNewsApi(Country $country, string $apiKey, bool $redirect = true): mixed
    {
        $response = Http::timeout(15)->get('https://newsapi.org/v2/everything', [
            'q'        => $country->country_name . ' supply chain OR economy OR trade OR logistics',
            'language' => 'en',
            'pageSize' => 5,
            'sortBy'   => 'publishedAt',
            'apiKey'   => $apiKey,
        ]);

        if (!$response->successful()) {
            $err = 'NewsAPI error: ' . $response->status();
            if ($redirect) return back()->with('error', $err);
            throw new \Exception($err);
        }

        $articles = $response->json()['articles'] ?? [];
        $imported = 0;

        foreach ($articles as $article) {
            $title = trim($article['title'] ?? '');
            $desc  = trim($article['description'] ?? '');
            if (!$title || $title === '[Removed]') continue;

            $sentiment = $this->analyzeSentiment($title . ' ' . $desc);

            News::updateOrCreate(
                ['country_id' => $country->id, 'title' => $title],
                [
                    'description'  => $desc,
                    'url'          => $article['url'] ?? null,
                    'source'       => $article['source']['name'] ?? 'NewsAPI',
                    'sentiment'    => $sentiment,
                    'published_at' => isset($article['publishedAt'])
                        ? \Carbon\Carbon::parse($article['publishedAt'])
                        : now(),
                ]
            );
            $imported++;
        }

        if ($redirect) {
            return back()->with('success', "✅ NewsAPI: {$imported} berita diimpor untuk {$country->country_name}.");
        }
        return $imported;
    }

    /**
     * ============================================================
     * SENTIMENT ANALYSIS — Lexicon-Based (sesuai spesifikasi)
     * Menggunakan kamus kata dari tabel positive_words & negative_words
     *
     * Algoritma:
     *   positiveScore++ jika kata ada di tabel positive_words
     *   negativeScore++ jika kata ada di tabel negative_words
     *   sentiment = positiveScore > negativeScore ? Positive : Negative
     * ============================================================
     */
    private function analyzeSentiment(string $text): string
    {
        $text = strtolower($text);

        // Ambil dari database
        $positiveWords = PositiveWord::pluck('word')->toArray();
        $negativeWords = NegativeWord::pluck('word')->toArray();

        // Fallback hardcoded jika tabel kosong
        if (empty($positiveWords)) {
            $positiveWords = [
                'growth', 'increase', 'improve', 'positive', 'gain', 'profit',
                'success', 'boost', 'strong', 'recovery', 'stable', 'progress',
                'advance', 'opportunity', 'innovation', 'investment', 'surplus',
                'expand', 'thrive', 'prosper', 'agreement', 'cooperation',
                'partnership', 'alliance', 'safety', 'relief', 'rebound',
            ];
        }

        if (empty($negativeWords)) {
            $negativeWords = [
                'crisis', 'decline', 'drop', 'fall', 'negative', 'loss',
                'risk', 'threat', 'collapse', 'recession', 'disruption',
                'shortage', 'conflict', 'war', 'sanction', 'inflation',
                'deficit', 'debt', 'disaster', 'corruption', 'tariff',
                'embargo', 'protest', 'shutdown', 'delay', 'strike',
                'blockade', 'scarcity', 'ban', 'failure', 'tension',
                'decrease', 'reduce', 'slow', 'weak', 'poor',
            ];
        }

        // Hitung skor
        $positiveScore = 0;
        $negativeScore = 0;

        foreach ($positiveWords as $word) {
            if (str_contains($text, strtolower($word))) {
                $positiveScore++;
            }
        }

        foreach ($negativeWords as $word) {
            if (str_contains($text, strtolower($word))) {
                $negativeScore++;
            }
        }

        if ($positiveScore > $negativeScore) return 'Positive';
        if ($negativeScore > $positiveScore) return 'Negative';
        return 'Neutral';
    }
}
