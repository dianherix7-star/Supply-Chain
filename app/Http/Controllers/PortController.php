<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PortController extends Controller
{
    public function index(Request $request)
    {
        $query = Port::with('country');

        if ($request->filled('search')) {
            $query->where('port_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $ports     = $query->orderBy('port_name')->paginate(20)->withQueryString();
        $countries = Country::orderBy('country_name')->get(['id', 'country_name', 'country_code']);

        // Semua port untuk peta - terfilter sesuai kriteria pencarian dan negara
        $mapQuery = Port::with('country:id,country_name,country_code,flag')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($request->filled('search')) {
            $mapQuery->where('port_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('country_id')) {
            $mapQuery->where('country_id', $request->country_id);
        }

        $allPortsForMap = $mapQuery->get(['id', 'port_name', 'country_id', 'latitude', 'longitude']);

        $stats = [
            'total'     => Port::count(),
            'countries' => Port::distinct('country_id')->count('country_id'),
            'with_coords' => Port::whereNotNull('latitude')->whereNotNull('longitude')->count(),
        ];

        return view('ports.index', compact('ports', 'countries', 'allPortsForMap', 'stats'));
    }

    /**
     * ======================================================
     * FETCH PELABUHAN DARI OVERPASS API (OpenStreetMap)
     * Gratis, tanpa API key — sumber data OSM komunitas global
     * ======================================================
     *
     * Query Overpass QL untuk pelabuhan besar dunia:
     *   node["harbour"="yes"]["name"]  — simpul bertag harbour
     *   node["seamark:type"="harbour"]["name"] — data nautical chart
     */
    public function fetchFromOSM(Request $request)
    {
        // Overpass API endpoint (gratis, open source)
        $overpassUrl = 'https://overpass-api.de/api/interpreter';

        // Query Overpass QL — ambil pelabuhan dengan nama, limit hasil
        $query = <<<'OSM'
[out:json][timeout:90];
(
  node["harbour"="yes"]["name"];
  node["seamark:type"="harbour"]["name"];
  node["seamark:type"="port"]["name"];
);
out 500;
OSM;

        try {
            $response = Http::timeout(100)
                ->withHeaders(['Accept' => 'application/json'])
                ->post($overpassUrl, ['data' => $query]);

            if (!$response->successful()) {
                return back()->with('error', 'Gagal menghubungi Overpass API. Status: ' . $response->status() . '. Coba lagi dalam beberapa menit.');
            }

            $json     = $response->json();
            $elements = $json['elements'] ?? [];

            if (empty($elements)) {
                return back()->with('error', 'Tidak ada data pelabuhan dari Overpass API. Coba lagi.');
            }

            // Cache country_code → country untuk efisiensi
            $countriesMap = Country::pluck('id', 'country_code')->toArray();

            $imported  = 0;
            $skipped   = 0;
            $noCountry = 0;

            foreach ($elements as $el) {
                $tags = $el['tags'] ?? [];
                $name = $tags['name'] ?? ($tags['name:en'] ?? null);
                $lat  = $el['lat']  ?? null;
                $lon  = $el['lon']  ?? null;

                // Lewati jika tidak ada nama atau koordinat
                if (!$name || !$lat || !$lon) {
                    $skipped++;
                    continue;
                }

                // Tentukan negara dari tag 'addr:country' atau 'is_in:country_code'
                $countryCode = $tags['addr:country']       // ISO 3166-1 alpha-2
                    ?? $tags['is_in:country_code']
                    ?? $tags['country_code']
                    ?? null;

                $countryId = null;

                if ($countryCode) {
                    $countryId = $countriesMap[strtoupper($countryCode)] ?? null;
                }

                // Fallback: match berdasarkan nama negara di tag
                if (!$countryId) {
                    $isIn = $tags['is_in'] ?? $tags['addr:country_name'] ?? null;
                    if ($isIn) {
                        $found = Country::where('country_name', 'like', '%' . $isIn . '%')
                            ->orWhere('country_code', strtoupper(substr($isIn, 0, 2)))
                            ->first();
                        if ($found) {
                            $countryId = $found->id;
                        }
                    }
                }

                if (!$countryId) {
                    $noCountry++;
                    continue;
                }

                // Simpan ke database (firstOrCreate agar tidak duplikat)
                Port::firstOrCreate(
                    [
                        'port_name'  => $name,
                        'country_id' => $countryId,
                    ],
                    [
                        'latitude'  => $lat,
                        'longitude' => $lon,
                    ]
                );

                $imported++;

            }

            $msg = "✅ Fetch dari Overpass API (OSM) selesai! "
                . "{$imported} pelabuhan berhasil diimpor "
                . "({$noCountry} tanpa relasi negara), "
                . "{$skipped} dilewati (tanpa nama/koordinat). "
                . "Total di database: " . Port::count() . " pelabuhan.";

            return back()->with('success', $msg);

        } catch (\Exception $e) {
            Log::error('Overpass API error: ' . $e->getMessage());
            return back()->with('error', 'Error saat menghubungi Overpass API: ' . $e->getMessage());
        }
    }

    /**
     * Fetch pelabuhan untuk satu negara tertentu via Overpass API
     */
    public function fetchByCountry(Country $country)
    {
        if (!$country->latitude || !$country->longitude) {
            return back()->with('error', "Negara {$country->country_name} tidak memiliki koordinat.");
        }

        // Buat bounding box sekitar negara (±15 derajat)
        $lat  = $country->latitude;
        $lon  = $country->longitude;
        $bbox = ($lat - 15) . ',' . ($lon - 20) . ',' . ($lat + 15) . ',' . ($lon + 20);

        $query = <<<OSM
[out:json][timeout:60];
(
  node["harbour"="yes"]["name"]({$bbox});
  node["seamark:type"="harbour"]["name"]({$bbox});
  node["seamark:type"="port"]["name"]({$bbox});
);
out 100;
OSM;

        try {
            $response = Http::timeout(70)
                ->post('https://overpass-api.de/api/interpreter', ['data' => $query]);

            if (!$response->successful()) {
                return back()->with('error', 'Gagal menghubungi Overpass API untuk ' . $country->country_name);
            }

            $elements  = $response->json()['elements'] ?? [];
            $imported  = 0;

            foreach ($elements as $el) {
                $tags = $el['tags'] ?? [];
                $name = $tags['name'] ?? ($tags['name:en'] ?? null);
                $lat2 = $el['lat'] ?? null;
                $lon2 = $el['lon'] ?? null;

                if (!$name || !$lat2 || !$lon2) continue;

                Port::firstOrCreate(
                    ['port_name' => $name, 'country_id' => $country->id],
                    ['latitude' => $lat2, 'longitude' => $lon2]
                );
                $imported++;
            }

            return back()->with('success',
                "✅ {$imported} pelabuhan berhasil diimpor untuk {$country->country_name} dari Overpass API (OSM)."
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $countries = Country::orderBy('country_name')->get(['id', 'country_name']);
        return view('ports.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'port_name'  => 'required|string|max:255',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'longitude'  => 'nullable|numeric|between:-180,180',
        ]);

        Port::create($request->only(['country_id', 'port_name', 'latitude', 'longitude']));
        return redirect()->route('ports.index')->with('success', 'Pelabuhan berhasil ditambahkan.');
    }

    public function edit(Port $port)
    {
        $countries = Country::orderBy('country_name')->get(['id', 'country_name']);
        return view('ports.edit', compact('port', 'countries'));
    }

    public function update(Request $request, Port $port)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'port_name'  => 'required|string|max:255',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'longitude'  => 'nullable|numeric|between:-180,180',
        ]);

        $port->update($request->only(['country_id', 'port_name', 'latitude', 'longitude']));
        return redirect()->route('ports.index')->with('success', 'Pelabuhan berhasil diupdate.');
    }

    public function destroy(Port $port)
    {
        $port->delete();
        return redirect()->route('ports.index')->with('success', 'Pelabuhan berhasil dihapus.');
    }
}
