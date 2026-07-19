<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherData;
use App\Models\News;
use App\Models\RiskScore;
use App\Models\Port;
use App\Models\EconomicData;
use App\Models\ExchangeRate;
use App\Models\Watchlist;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('user.dashboard');
        }

        $stats = [
            'total_countries'  => Country::count(),
            'total_weather'    => WeatherData::count(),
            'total_news'       => News::count(),
            'total_risk'       => RiskScore::count(),
            'total_ports'      => Port::count(),
            'total_economic'   => EconomicData::distinct('country_id')->count('country_id'),
            'total_articles'   => Article::count(),
            'high_risk'        => RiskScore::where('risk_level', 'High')->count(),
            'medium_risk'      => RiskScore::where('risk_level', 'Medium')->count(),
            'low_risk'         => RiskScore::where('risk_level', 'Low')->count(),
            'avg_risk_score'   => round(RiskScore::avg('total_score'), 1),
            'positive_news'    => News::where('sentiment', 'Positive')->count(),
            'negative_news'    => News::where('sentiment', 'Negative')->count(),
        ];

        // Top 5 negara risiko tertinggi untuk chart
        $topRiskCountries = RiskScore::with('country')
            ->orderByDesc('total_score')
            ->limit(5)
            ->get();

        // Recent articles
        $recentArticles = Article::latest()->limit(3)->get();

        // Recent news
        $recentNews = News::with('country')->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'topRiskCountries', 'recentArticles', 'recentNews'));
    }

    public function user()
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $userId = Auth::id();

        $watchlists = Watchlist::with([
            'country.riskScore',
            'country.weatherData',
        ])->where('user_id', $userId)->get();

        $countries = Country::orderBy('country_name')->get(['id', 'country_name', 'country_code', 'flag']);

        $stats = [
            'total_watchlist' => $watchlists->count(),
            'high_risk'       => $watchlists->filter(fn($w) => $w->country?->riskScore?->risk_level === 'High')->count(),
            'medium_risk'     => $watchlists->filter(fn($w) => $w->country?->riskScore?->risk_level === 'Medium')->count(),
            'low_risk'        => $watchlists->filter(fn($w) => $w->country?->riskScore?->risk_level === 'Low')->count(),
        ];

        return view('user.dashboard', compact('watchlists', 'countries', 'stats'));
    }
}