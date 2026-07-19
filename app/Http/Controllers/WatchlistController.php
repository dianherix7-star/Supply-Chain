<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Watchlist;
use App\Models\RiskScore;
use App\Models\WeatherData;
use App\Models\ExchangeRate;
use App\Models\EconomicData;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function index()
    {
        $watchlists = Watchlist::with(['country.riskScore', 'country.weatherData'])
            ->where('user_id', Auth::id())
            ->get();

        $stats = [
            'total_watchlist' => $watchlists->count(),
            'high_risk' => 0,
            'medium_risk' => 0,
            'low_risk' => 0,
        ];

        foreach ($watchlists as $item) {
            $risk = $item->country?->riskScore;
            if ($risk) {
                if ($risk->risk_level === 'High') {
                    $stats['high_risk']++;
                } elseif ($risk->risk_level === 'Medium') {
                    $stats['medium_risk']++;
                } elseif ($risk->risk_level === 'Low') {
                    $stats['low_risk']++;
                }
            }
        }

        $countries = Country::orderBy('country_name')->get();

        return view('watchlist.index', compact('watchlists', 'stats', 'countries'));
    }

    public function add(Request $request)
    {
        $request->validate(['country_id' => 'required|exists:countries,id']);

        // Cek duplikat
        $exists = Watchlist::where('user_id', Auth::id())
            ->where('country_id', $request->country_id)
            ->exists();

        if ($exists) {
            return back()->with('info', 'Negara sudah ada di watchlist Anda.');
        }

        Watchlist::create([
            'user_id'    => Auth::id(),
            'country_id' => $request->country_id,
        ]);

        $country = Country::find($request->country_id);
        return back()->with('success', "✅ {$country->country_name} ditambahkan ke watchlist.");
    }

    public function remove(Country $country)
    {
        Watchlist::where('user_id', Auth::id())
            ->where('country_id', $country->id)
            ->delete();

        return back()->with('success', "{$country->country_name} dihapus dari watchlist.");
    }
}

