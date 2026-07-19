<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\WeatherData;
use App\Models\EconomicData;
use App\Models\ExchangeRate;
use App\Models\News;
use App\Support\CurrencyHelper;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('country_name')->get(['id', 'country_name', 'country_code', 'flag', 'region']);
        return view('compare.index', compact('countries'));
    }

    public function result(Request $request)
    {
        $request->validate([
            'country_a' => 'required|exists:countries,id',
            'country_b' => 'required|exists:countries,id|different:country_a',
        ]);

        $countryA = $this->getCountryData($request->country_a);
        $countryB = $this->getCountryData($request->country_b);
        $countries = Country::orderBy('country_name')->get(['id', 'country_name', 'country_code', 'flag', 'region']);

        return view('compare.index', compact('countryA', 'countryB', 'countries'));
    }

    private function getCountryData(int $id): array
    {
        $country   = Country::find($id);
        $risk      = RiskScore::where('country_id', $id)->first();
        $weather   = WeatherData::where('country_id', $id)->latest('recorded_at')->first();
        $economic  = EconomicData::where('country_id', $id)->latest('year')->first();

        // Ambil kurs dari currency pertama negara
        $exchangeRate = null;
        $currCode = CurrencyHelper::primaryCode($country->currency);
        if ($currCode) {
            $exchangeRate = ExchangeRate::where('currency', $currCode)->first();
        }

        // Sentiment news
        $totalNews = News::where('country_id', $id)->count();
        $negNews   = News::where('country_id', $id)->where('sentiment', 'Negative')->count();
        $posNews   = News::where('country_id', $id)->where('sentiment', 'Positive')->count();

        return [
            'country'      => $country,
            'risk'         => $risk,
            'weather'      => $weather,
            'economic'     => $economic,
            'exchange'     => $exchangeRate,
            'total_news'   => $totalNews,
            'neg_news'     => $negNews,
            'pos_news'     => $posNews,
        ];
    }
}
