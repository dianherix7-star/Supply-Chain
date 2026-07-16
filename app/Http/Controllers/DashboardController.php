<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherData;
use App\Models\News;
use App\Models\RiskScore;

class DashboardController extends Controller
{
    public function admin()
    {
        $stats = [
            'total_countries'  => Country::count(),
            'total_weather'    => WeatherData::count(),
            'total_news'       => News::count(),
            'total_risk'       => RiskScore::count(),
            'high_risk'        => RiskScore::where('risk_level', 'High')->count(),
            'medium_risk'      => RiskScore::where('risk_level', 'Medium')->count(),
            'low_risk'         => RiskScore::where('risk_level', 'Low')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function user()
    {
        return view('user.dashboard');
    }
}