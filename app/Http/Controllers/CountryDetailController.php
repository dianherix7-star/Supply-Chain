<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\WeatherData;
use App\Models\EconomicData;
use App\Models\News;
use Illuminate\Support\Facades\Auth;
use App\Models\Watchlist;

class CountryDetailController extends Controller
{
    public function show(Country $country)
    {
        // Semua data terkait
        $weather         = WeatherData::where('country_id', $country->id)->latest('recorded_at')->first();
        $riskScore       = RiskScore::where('country_id', $country->id)->latest()->first();
        $latestEco       = EconomicData::where('country_id', $country->id)->orderBy('year', 'desc')->first();
        $economicHistory = EconomicData::where('country_id', $country->id)->orderBy('year', 'asc')->get();
        $latestNews      = News::where('country_id', $country->id)->latest('published_at')->limit(6)->get();
        $ports           = $country->ports;

        // Cek watchlist
        $inWatchlist = Auth::check()
            ? Watchlist::where('user_id', Auth::id())->where('country_id', $country->id)->exists()
            : false;

        return view('countries.show', compact(
            'country', 'weather', 'riskScore', 'latestEco',
            'economicHistory', 'latestNews', 'ports', 'inWatchlist'
        ));
    }
}
