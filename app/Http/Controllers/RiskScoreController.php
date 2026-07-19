<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherData;
use App\Models\ExchangeRate;
use App\Models\News;
use App\Models\RiskScore;
use App\Support\CurrencyHelper;
use Illuminate\Http\Request;

class RiskScoreController extends Controller
{
    /**
     * Tampilkan dashboard risk score
     */
    public function index(Request $request)
    {
        $query = RiskScore::with('country')->latest();

        if ($request->filled('search')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('country_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }

        $riskScores = $query->paginate(15)->withQueryString();

        // Statistik
        $stats = [
            'total'      => RiskScore::count(),
            'high'       => RiskScore::where('risk_level', 'High')->count(),
            'medium'     => RiskScore::where('risk_level', 'Medium')->count(),
            'low'        => RiskScore::where('risk_level', 'Low')->count(),
            'avg_score'  => round(RiskScore::avg('total_score'), 1),
        ];

        return view('risk.index', compact('riskScores', 'stats'));
    }

    /**
     * Hitung risk score untuk semua negara yang punya data
     *
     * Formula:
     *  - weather_score  (0-25): dari storm_risk dan kondisi cuaca
     *  - inflation_score (0-25): dari kestabilan exchange rate vs USD
     *  - currency_score (0-25): dari kekuatan mata uang vs USD
     *  - news_score     (0-25): dari rasio berita negatif
     *  - total_score    (0-100): sum semua komponen
     *  - risk_level: Low (0-40), Medium (41-70), High (71-100)
     */
    public function calculateAll()
    {
        $countries = Country::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        if ($countries->isEmpty()) {
            return back()->with('error', 'Tidak ada data negara. Import countries terlebih dahulu.');
        }

        $success = 0;

        foreach ($countries as $country) {
            // Hitung tiap komponen
            $weatherScore  = $this->calcWeatherScore($country);
            $currencyScore = $this->calcCurrencyScore($country);
            $newsScore     = $this->calcNewsScore($country);

            // Inflation score berdasarkan economic data (jika ada) atau currency volatility
            $inflationScore = $this->calcInflationScore($country);

            $totalScore = $weatherScore + $inflationScore + $currencyScore + $newsScore;

            // Clamp ke 0-100
            $totalScore = max(0, min(100, $totalScore));

            // Tentukan risk level
            if ($totalScore >= 71) {
                $riskLevel = 'High';
            } elseif ($totalScore >= 41) {
                $riskLevel = 'Medium';
            } else {
                $riskLevel = 'Low';
            }

            RiskScore::updateOrCreate(
                ['country_id' => $country->id],
                [
                    'weather_score'   => $weatherScore,
                    'inflation_score' => $inflationScore,
                    'currency_score'  => $currencyScore,
                    'news_score'      => $newsScore,
                    'total_score'     => $totalScore,
                    'risk_level'      => $riskLevel,
                ]
            );

            $success++;
        }

        return back()->with('success', "✅ Kalkulasi selesai! {$success} negara dihitung.");
    }

    /**
     * Hitung risk score satu negara
     */
    public function calculateOne(Country $country)
    {
        $weatherScore   = $this->calcWeatherScore($country);
        $currencyScore  = $this->calcCurrencyScore($country);
        $newsScore      = $this->calcNewsScore($country);
        $inflationScore = $this->calcInflationScore($country);

        $totalScore = max(0, min(100, $weatherScore + $inflationScore + $currencyScore + $newsScore));

        if ($totalScore >= 71) {
            $riskLevel = 'High';
        } elseif ($totalScore >= 41) {
            $riskLevel = 'Medium';
        } else {
            $riskLevel = 'Low';
        }

        RiskScore::updateOrCreate(
            ['country_id' => $country->id],
            [
                'weather_score'   => $weatherScore,
                'inflation_score' => $inflationScore,
                'currency_score'  => $currencyScore,
                'news_score'      => $newsScore,
                'total_score'     => $totalScore,
                'risk_level'      => $riskLevel,
            ]
        );

        return back()->with('success', "✅ Risk score untuk {$country->country_name}: {$totalScore}/100 ({$riskLevel})");
    }

    /**
     * Weather Score (0–25)
     * Semakin tinggi storm_risk, wind_speed, rainfall → semakin tinggi skor
     */
    private function calcWeatherScore(Country $country): int
    {
        $weather = WeatherData::where('country_id', $country->id)->latest('recorded_at')->first();

        if (!$weather) return 10; // Default sedang jika tidak ada data

        $score = 0;

        // Storm risk (0-5) → kontribusi max 15
        $score += ($weather->storm_risk ?? 0) * 3;

        // Wind speed → di atas 40 km/h = berbahaya
        $windSpeed = $weather->wind_speed ?? 0;
        if ($windSpeed >= 50) $score += 5;
        elseif ($windSpeed >= 30) $score += 3;
        elseif ($windSpeed >= 15) $score += 1;

        // Extreme temperature (di bawah -10 atau di atas 40)
        $temp = $weather->temperature ?? 20;
        if ($temp >= 45 || $temp <= -15) $score += 5;
        elseif ($temp >= 40 || $temp <= -5) $score += 3;

        return min(25, $score);
    }

    /**
     * Currency Score (0–25)
     * Mata uang yang sangat lemah terhadap USD = risiko tinggi
     */
    private function calcCurrencyScore(Country $country): int
    {
        if (empty($country->currency)) return 10;

        $currencyCode = CurrencyHelper::primaryCode($country->currency);

        if (!$currencyCode) return 10;

        $rate = ExchangeRate::where('currency', $currencyCode)->first();

        if (!$rate) return 10; // Default

        $exchangeRate = $rate->exchange_rate;

        // Skor berdasarkan seberapa lemah vs USD
        if ($exchangeRate >= 10000)  return 25;  // Sangat lemah (misal IDR)
        if ($exchangeRate >= 1000)   return 20;
        if ($exchangeRate >= 100)    return 15;
        if ($exchangeRate >= 10)     return 10;
        if ($exchangeRate >= 1)      return 5;

        return 2; // Lebih kuat dari USD
    }

    /**
     * Inflation Score (0–25)
     * Berdasarkan economic data atau estimasi dari exchange rate
     */
    private function calcInflationScore(Country $country): int
    {
        // Cek apakah ada economic data
        $eco = $country->economicData()->latest('year')->first();

        if ($eco && $eco->inflation !== null) {
            $inflation = abs($eco->inflation);

            if ($inflation >= 20) return 25;
            if ($inflation >= 10) return 20;
            if ($inflation >= 5)  return 15;
            if ($inflation >= 3)  return 10;
            return 5;
        }

        // Fallback: estimasi dari region
        $region = $country->region ?? '';

        return match($region) {
            'Africa'   => 15,
            'Americas' => 12,
            'Asia'     => 10,
            'Europe'   => 8,
            'Oceania'  => 7,
            default    => 10,
        };
    }

    /**
     * News Score (0–25)
     * Semakin banyak berita negatif → skor tinggi
     */
    private function calcNewsScore(Country $country): int
    {
        $totalNews = News::where('country_id', $country->id)->count();

        if ($totalNews === 0) return 10; // Default

        $negativeNews = News::where('country_id', $country->id)
            ->where('sentiment', 'Negative')
            ->count();

        $positiveNews = News::where('country_id', $country->id)
            ->where('sentiment', 'Positive')
            ->count();

        $ratio = $negativeNews / $totalNews;

        // Juga pertimbangkan rasio positif
        $score = (int) round($ratio * 25);

        // Kurangi skor jika ada berita positif yang dominan
        $positiveRatio = $totalNews > 0 ? $positiveNews / $totalNews : 0;
        if ($positiveRatio > 0.6) {
            $score = max(0, $score - 5);
        }

        return min(25, max(0, $score));
    }
}
