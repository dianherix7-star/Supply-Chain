<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\RiskScore;
use App\Models\Port;
use App\Models\News;
use App\Models\ExchangeRate;
use App\Models\EconomicData;
use App\Models\WeatherData;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * GET /api/countries
     * Daftar semua negara beserta risk score
     */
    public function countries(Request $request)
    {
        $query = Country::with(['riskScore', 'weatherData', 'economicData' => fn($q) => $q->latest('year')->limit(1)]);

        if ($request->filled('search')) {
            $query->where('country_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        $countries = $query->orderBy('country_name')->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $countries->map(function ($c) {
                return [
                    'id'           => $c->id,
                    'country_code' => $c->country_code,
                    'country_name' => $c->country_name,
                    'capital'      => $c->capital,
                    'region'       => $c->region,
                    'subregion'    => $c->subregion,
                    'currency'     => $c->currency,
                    'flag'         => $c->flag,
                    'latitude'     => $c->latitude,
                    'longitude'    => $c->longitude,
                    'risk_score'   => $c->riskScore ? [
                        'total_score'  => $c->riskScore->total_score,
                        'risk_level'   => $c->riskScore->risk_level,
                    ] : null,
                ];
            }),
            'meta' => [
                'total'        => $countries->total(),
                'per_page'     => $countries->perPage(),
                'current_page' => $countries->currentPage(),
                'last_page'    => $countries->lastPage(),
            ]
        ]);
    }

    /**
     * GET /api/risk
     * Data risk score semua negara
     */
    public function risk(Request $request)
    {
        $query = RiskScore::with('country');

        if ($request->filled('level')) {
            $query->where('risk_level', $request->level);
        }

        $risks = $query->orderByDesc('total_score')->get();

        return response()->json([
            'success' => true,
            'count'   => $risks->count(),
            'data'    => $risks->map(function ($r) {
                return [
                    'country'        => $r->country?->country_name,
                    'country_code'   => $r->country?->country_code,
                    'weather_score'  => $r->weather_score,
                    'inflation_score'=> $r->inflation_score,
                    'currency_score' => $r->currency_score,
                    'news_score'     => $r->news_score,
                    'total_score'    => $r->total_score,
                    'risk_level'     => $r->risk_level,
                ];
            })
        ]);
    }

    /**
     * GET /api/ports
     * Daftar pelabuhan dengan koordinat
     */
    public function ports(Request $request)
    {
        $query = Port::with('country:id,country_name,country_code,flag');

        if ($request->filled('search')) {
            $query->where('port_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $ports = $query->orderBy('port_name')->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'data'    => $ports->map(function ($p) {
                return [
                    'id'           => $p->id,
                    'port_name'    => $p->port_name,
                    'country'      => $p->country?->country_name,
                    'country_code' => $p->country?->country_code,
                    'latitude'     => $p->latitude,
                    'longitude'    => $p->longitude,
                ];
            }),
            'meta' => [
                'total'    => $ports->total(),
                'per_page' => $ports->perPage(),
            ]
        ]);
    }

    /**
     * GET /api/news
     * Berita terbaru + sentiment
     */
    public function news(Request $request)
    {
        $query = News::with('country:id,country_name,country_code');

        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $news = $query->latest('published_at')->paginate($request->get('per_page', 20));

        // Sentiment summary
        $total    = News::count();
        $positive = News::where('sentiment', 'Positive')->count();
        $negative = News::where('sentiment', 'Negative')->count();

        return response()->json([
            'success'  => true,
            'summary'  => [
                'total'    => $total,
                'positive' => $positive,
                'negative' => $negative,
                'neutral'  => max(0, $total - $positive - $negative),
            ],
            'data' => $news->map(function ($n) {
                return [
                    'id'           => $n->id,
                    'title'        => $n->title,
                    'source'       => $n->source,
                    'sentiment'    => $n->sentiment,
                    'url'          => $n->url,
                    'country'      => $n->country?->country_name,
                    'published_at' => $n->published_at,
                ];
            }),
            'meta' => [
                'total'    => $news->total(),
                'per_page' => $news->perPage(),
            ]
        ]);
    }

    /**
     * GET /api/currency
     * Data kurs mata uang vs USD
     */
    public function currency(Request $request)
    {
        $rates = ExchangeRate::orderBy('currency')->get();

        return response()->json([
            'success'    => true,
            'base'       => 'USD',
            'count'      => $rates->count(),
            'data'       => $rates->map(function ($r) {
                return [
                    'currency'      => $r->currency,
                    'exchange_rate' => $r->exchange_rate,
                    'updated_at'    => $r->updated_at,
                ];
            })
        ]);
    }

    /**
     * GET /api/weather
     * Data cuaca terbaru per negara
     */
    public function weather(Request $request)
    {
        $query = WeatherData::with('country:id,country_name,country_code,flag,latitude,longitude');

        if ($request->filled('storm_min')) {
            $query->where('storm_risk', '>=', $request->storm_min);
        }

        $weather = $query->latest('recorded_at')->paginate($request->get('per_page', 30));

        return response()->json([
            'success' => true,
            'data'    => $weather->map(function ($w) {
                return [
                    'country'     => $w->country?->country_name,
                    'country_code'=> $w->country?->country_code,
                    'latitude'    => $w->country?->latitude,
                    'longitude'   => $w->country?->longitude,
                    'temperature' => $w->temperature,
                    'rainfall'    => $w->rainfall,
                    'wind_speed'  => $w->wind_speed,
                    'storm_risk'  => $w->storm_risk,
                    'recorded_at' => $w->recorded_at,
                ];
            }),
            'meta' => ['total' => $weather->total()]
        ]);
    }

    /**
     * GET /api/economic
     * Data ekonomi (GDP, inflasi, dll)
     */
    public function economic(Request $request)
    {
        $query = EconomicData::with('country:id,country_name,country_code')
            ->latest('year');

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $eco = $query->paginate($request->get('per_page', 30));

        return response()->json([
            'success' => true,
            'data'    => $eco->map(function ($e) {
                return [
                    'country'    => $e->country?->country_name,
                    'year'       => $e->year,
                    'gdp'        => $e->gdp,
                    'inflation'  => $e->inflation,
                    'population' => $e->population,
                    'exports'    => $e->exports,
                    'imports'    => $e->imports,
                ];
            }),
            'meta' => ['total' => $eco->total()]
        ]);
    }
}
