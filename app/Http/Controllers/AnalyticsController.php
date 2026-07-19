<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\EconomicData;
use App\Models\News;
use App\Models\ExchangeRate;
use App\Models\WeatherData;
use App\Models\Port;

class AnalyticsController extends Controller
{
    public function index()
    {
        // --- Top 10 Negara Risiko Tertinggi ---
        $topRisk = RiskScore::with('country')
            ->orderByDesc('total_score')
            ->limit(10)
            ->get();

        // --- Distribusi Risk Level ---
        $riskDistribution = [
            'High'   => RiskScore::where('risk_level', 'High')->count(),
            'Medium' => RiskScore::where('risk_level', 'Medium')->count(),
            'Low'    => RiskScore::where('risk_level', 'Low')->count(),
        ];

        // --- GDP Top 10 Negara (data terbaru per negara) ---
        $gdpData = EconomicData::with('country')
            ->whereNotNull('gdp')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('economic_data')
                    ->groupBy('country_id');
            })
            ->orderByDesc('gdp')
            ->limit(10)
            ->get();

        // --- Inflasi Top 10 Tertinggi ---
        $inflationData = EconomicData::with('country')
            ->whereNotNull('inflation')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('economic_data')
                    ->groupBy('country_id');
            })
            ->orderByDesc('inflation')
            ->limit(10)
            ->get();

        // --- Sentiment Analysis Summary ---
        $totalNews    = News::count();
        $positiveNews = News::where('sentiment', 'Positive')->count();
        $negativeNews = News::where('sentiment', 'Negative')->count();
        $neutralNews  = News::where('sentiment', 'Neutral')->count();

        $sentimentStats = [
            'total'    => $totalNews,
            'positive' => $positiveNews,
            'negative' => $negativeNews,
            'neutral'  => $neutralNews,
            'pos_pct'  => $totalNews > 0 ? round(($positiveNews / $totalNews) * 100, 1) : 0,
            'neg_pct'  => $totalNews > 0 ? round(($negativeNews / $totalNews) * 100, 1) : 0,
            'neu_pct'  => $totalNews > 0 ? round(($neutralNews  / $totalNews) * 100, 1) : 0,
        ];

        // --- Currency: Top 10 Mata Uang Paling Lemah ---
        $weakCurrencies = ExchangeRate::orderByDesc('exchange_rate')->limit(10)->get();

        // --- Stats Global ---
        $globalStats = [
            'total_countries' => Country::count(),
            'total_ports'     => Port::count(),
            'total_news'      => $totalNews,
            'total_risk'      => RiskScore::count(),
            'avg_risk_score'  => round(RiskScore::avg('total_score'), 1),
            'total_economic'  => EconomicData::distinct('country_id')->count('country_id'),
        ];

        // --- Weather: negara dengan storm risk tertinggi ---
        $highRiskWeather = WeatherData::with('country')
            ->where('storm_risk', '>=', 3)
            ->orderByDesc('storm_risk')
            ->limit(10)
            ->get();

        // --- Risk Score per Region ---
        $riskByRegion = RiskScore::query()
            ->join('countries', 'countries.id', '=', 'risk_scores.country_id')
            ->selectRaw('countries.region, AVG(risk_scores.total_score) as avg_score')
            ->groupBy('countries.region')
            ->orderByDesc('avg_score')
            ->pluck('avg_score', 'region')
            ->map(fn ($score) => round((float) $score, 1));

        return view('analytics.index', compact(
            'topRisk',
            'riskDistribution',
            'gdpData',
            'inflationData',
            'sentimentStats',
            'weakCurrencies',
            'globalStats',
            'highRiskWeather',
            'riskByRegion'
        ));
    }
}
