<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CountryController extends Controller
{
    // Menampilkan semua data country (dengan fitur search)
    public function index(Request $request)
    {
        $query = Country::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('country_name', 'like', '%' . $search . '%')
                  ->orWhere('country_code', 'like', '%' . $search . '%')
                  ->orWhere('region', 'like', '%' . $search . '%');
            });
        }

        $countries = $query->latest()->paginate(10)->withQueryString();

        return view('countries.index', compact('countries'));
    }

    // Form tambah country
    public function create()
    {
        return view('countries.create');
    }

    // Simpan country
    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10|unique:countries,country_code',
        ]);

        Country::create([
            'country_name' => $request->country_name,
            'country_code' => strtoupper($request->country_code),
            'capital'      => $request->capital,
            'region'       => $request->region,
            'subregion'    => $request->subregion,
            'population'   => $request->population,
            'currency'     => $request->currency,
            'flag'         => $request->flag,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
        ]);

        return redirect()->route('countries.index')
            ->with('success', 'Country berhasil ditambahkan.');
    }

    // Form edit
    public function edit(Country $country)
    {
        return view('countries.edit', compact('country'));
    }

    // Update country
    public function update(Request $request, Country $country)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10|unique:countries,country_code,' . $country->id,
        ]);

        $country->update([
            'country_name' => $request->country_name,
            'country_code' => strtoupper($request->country_code),
            'capital'      => $request->capital,
            'region'       => $request->region,
            'subregion'    => $request->subregion,
            'population'   => $request->population,
            'currency'     => $request->currency,
            'flag'         => $request->flag,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
        ]);

        return redirect()->route('countries.index')
            ->with('success', 'Country berhasil diupdate.');
    }

    // Hapus country
    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('countries.index')
            ->with('success', 'Country berhasil dihapus.');
    }

    // Import Country dari REST Countries API v3.1
    public function import()
    {
        try {
            // REST Countries v3.1 - menyediakan population, latlng, currencies, flags, dll.
            $response = Http::timeout(60)->get(
                'https://restcountries.com/v3.1/all',
                ['fields' => 'name,cca2,capital,region,subregion,population,currencies,flags,latlng']
            );

            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data dari REST Countries API. Status: ' . $response->status());
            }

            $countries = $response->json();

            if (empty($countries)) {
                return back()->with('error', 'Data dari API kosong.');
            }

            $imported = 0;
            $skipped  = 0;

            foreach ($countries as $item) {
                $code = $item['cca2'] ?? '';

                if (empty($code)) {
                    $skipped++;
                    continue;
                }

                // Ambil kode mata uang (misal: "USD, EUR")
                $currency = null;
                if (!empty($item['currencies'])) {
                    $currency = implode(', ', array_keys($item['currencies']));
                }

                // Gunakan flag PNG dari flagcdn (lebih cepat) atau fallback dari API
                $flagUrl = 'https://flagcdn.com/w80/' . strtolower($code) . '.png';

                Country::updateOrCreate(
                    ['country_code' => $code],
                    [
                        'country_name' => $item['name']['common']
                            ?? $item['name']['official']
                            ?? 'Unknown',
                        'capital'    => $item['capital'][0] ?? null,
                        'region'     => $item['region'] ?? null,
                        'subregion'  => $item['subregion'] ?? null,
                        'population' => $item['population'] ?? null,
                        'currency'   => $currency,
                        'flag'       => $flagUrl,
                        'latitude'   => $item['latlng'][0] ?? null,
                        'longitude'  => $item['latlng'][1] ?? null,
                    ]
                );

                $imported++;
            }

            return redirect()->route('countries.index')
                ->with('success', "✅ Import selesai! {$imported} negara berhasil diimport, {$skipped} dilewati.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}