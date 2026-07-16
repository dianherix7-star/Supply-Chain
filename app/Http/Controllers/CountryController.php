<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CountryController extends Controller
{
    // Menampilkan semua data country
    public function index()
    {
        $countries = Country::latest()->paginate(10);

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
            'country_name' => 'required',
            'country_code' => 'required',
        ]);

        Country::create([
            'country_name' => $request->country_name,
            'country_code' => $request->country_code,
            'capital'      => $request->capital,
            'region'       => $request->region,
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
            'country_name' => 'required',
            'country_code' => 'required',
        ]);

        $country->update([
            'country_name' => $request->country_name,
            'country_code' => $request->country_code,
            'capital'      => $request->capital,
            'region'       => $request->region,
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

    // Import Country dari API
    public function import()
    {
        try {

            $response = Http::timeout(60)->get(
                'https://raw.githubusercontent.com/mledoze/countries/master/countries.json'
            );

            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data Country.');
            }

            $countries = $response->json();

            foreach ($countries as $item) {

                Country::updateOrCreate(

                    [
                        'country_code' => $item['cca2'] ?? '',
                    ],

                    [

                        'country_name' => $item['name']['common']
                            ?? $item['name']['official']
                            ?? '',

                        'capital' => $item['capital'][0] ?? null,

                        'region' => $item['region'] ?? null,

                        // Population tidak tersedia pada JSON ini
                        'population' => null,

                        'currency' => isset($item['currencies'])
                            ? implode(',', array_keys($item['currencies']))
                            : null,

                        // Emoji bendera
                       'flag' => 'https://flagcdn.com/w80/' . strtolower($item['cca2']) . '.png',

                        'latitude' => $item['latlng'][0] ?? null,

                        'longitude' => $item['latlng'][1] ?? null,

                    ]

                );

            }

            return redirect()->route('countries.index')
                ->with('success', 'Data Country berhasil diimport.');

        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());

        }
    }
}