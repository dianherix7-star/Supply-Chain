<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\EconomicData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EconomicDataController extends Controller
{
    /**
     * Fetch data ekonomi dari World Bank API (gratis, tanpa API key)
     * Indikator:
     *   NY.GDP.MKTP.CD = GDP (current USD)
     *   FP.CPI.TOTL.ZG = Inflation (CPI annual %)
     *   SP.POP.TOTL     = Population
     *   NE.EXP.GNFS.CD  = Exports of goods and services (USD)
     *   NE.IMP.GNFS.CD  = Imports of goods and services (USD)
     */
    private array $indicators = [
        'gdp'        => 'NY.GDP.MKTP.CD',
        'inflation'  => 'FP.CPI.TOTL.ZG',
        'population' => 'SP.POP.TOTL',
        'exports'    => 'NE.EXP.GNFS.CD',
        'imports'    => 'NE.IMP.GNFS.CD',
    ];

    /**
     * Tampilkan halaman Economic Data
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $query = EconomicData::with('country')->latest('year');

        if ($request->filled('search')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('country_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $economicData = $query->paginate(15)->withQueryString();

        $years = EconomicData::distinct()->orderByDesc('year')->pluck('year');

        $stats = [
            'total'      => EconomicData::distinct('country_id')->count('country_id'),
            'avg_gdp'    => EconomicData::whereNotNull('gdp')->avg('gdp'),
            'avg_inf'    => round(EconomicData::whereNotNull('inflation')->avg('inflation'), 2),
            'latest_year'=> EconomicData::max('year'),
        ];

        $countries = Country::orderBy('country_name')->get(['id', 'country_name', 'country_code']);

        return view('economic.index', compact('economicData', 'years', 'stats', 'countries'));
    }

    /**
     * Fetch untuk SATU negara
     */
    public function fetchOne(Country $country)
    {
        if (empty($country->country_code)) {
            return back()->with('error', "Negara {$country->country_name} tidak memiliki country code.");
        }

        try {
            $data = $this->fetchWorldBank($country->country_code);

            if (empty($data)) {
                return back()->with('error', "Tidak ada data World Bank untuk {$country->country_name}.");
            }

            EconomicData::updateOrCreate(
                ['country_id' => $country->id, 'year' => $data['year']],
                [
                    'gdp'        => $data['gdp'],
                    'inflation'  => $data['inflation'],
                    'population' => $data['population'],
                    'exports'    => $data['exports'],
                    'imports'    => $data['imports'],
                ]
            );

            return back()->with('success', "✅ Data ekonomi {$country->country_name} ({$data['year']}) berhasil diperbarui.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Fetch untuk semua negara (batch, limit 30 untuk menghindari timeout)
     */
    public function fetchAll()
    {
        $countries = Country::whereNotNull('country_code')
            ->limit(30)
            ->get();

        if ($countries->isEmpty()) {
            return back()->with('error', 'Tidak ada data negara. Import countries terlebih dahulu.');
        }

        $success = 0;
        $failed  = 0;

        foreach ($countries as $country) {
            try {
                $data = $this->fetchWorldBank($country->country_code);

                if (!empty($data)) {
                    EconomicData::updateOrCreate(
                        ['country_id' => $country->id, 'year' => $data['year']],
                        [
                            'gdp'        => $data['gdp'],
                            'inflation'  => $data['inflation'],
                            'population' => $data['population'],
                            'exports'    => $data['exports'],
                            'imports'    => $data['imports'],
                        ]
                    );
                    $success++;
                } else {
                    $failed++;
                }

            } catch (\Exception $e) {
                $failed++;
            }
        }

        return back()->with('success', "✅ Fetch selesai! {$success} negara berhasil, {$failed} tidak ada data.");
    }

    /**
     * Ambil data dari World Bank API
     * @return array|null
     */
    private function fetchWorldBank(string $countryCode): ?array
    {
        $result  = [];
        $latestYear = null;

        foreach ($this->indicators as $field => $indicator) {
            // World Bank API endpoint
            $url = "https://api.worldbank.org/v2/country/{$countryCode}/indicator/{$indicator}";

            $response = Http::timeout(15)->get($url, [
                'format'    => 'json',
                'mrv'       => 1,      // most recent value
                'per_page'  => 1,
            ]);

            if (!$response->successful()) continue;

            $json = $response->json();

            // World Bank API returns array: [metadata, data]
            $data = $json[1] ?? [];

            if (empty($data)) continue;

            $entry = $data[0];
            $value = $entry['value'] ?? null;
            $year  = (int) ($entry['date'] ?? date('Y') - 1);

            if ($value !== null) {
                $result[$field] = $value;
                $latestYear = $year;
            }
        }

        if (empty($result) || !$latestYear) {
            return null;
        }

        $result['year'] = $latestYear;

        // Fill missing fields with null
        foreach (['gdp', 'inflation', 'population', 'exports', 'imports'] as $f) {
            $result[$f] = $result[$f] ?? null;
        }

        return $result;
    }
}
