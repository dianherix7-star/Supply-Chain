<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    /**
     * Tampilkan daftar data cuaca semua negara
     */
    public function index(Request $request)
    {
        $query = WeatherData::with('country')->latest('recorded_at');

        if ($request->filled('search')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('country_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('region')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('region', $request->region);
            });
        }

        $weatherData = $query->paginate(15)->withQueryString();

        // Untuk filter dropdown region
        $regions = Country::whereNotNull('region')
            ->distinct()
            ->orderBy('region')
            ->pluck('region');

        // Statistik ringkasan
        $stats = [
            'total'       => WeatherData::count(),
            'avg_temp'    => round(WeatherData::avg('temperature'), 1),
            'avg_wind'    => round(WeatherData::avg('wind_speed'), 1),
            'high_storm'  => WeatherData::where('storm_risk', '>=', 3)->count(),
        ];

        return view('weather.index', compact('weatherData', 'regions', 'stats'));
    }

    /**
     * Fetch data cuaca dari Open-Meteo untuk SEMUA negara (yang punya lat/lng)
     * Open-Meteo: gratis, tidak butuh API key
     */
    public function fetchAll()
    {
        // Ambil negara yang punya koordinat
        $countries = Country::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        if ($countries->isEmpty()) {
            return back()->with('error', 'Tidak ada data negara dengan koordinat. Silakan import countries terlebih dahulu.');
        }

        $success = 0;
        $failed  = 0;

        foreach ($countries as $country) {
            try {
                $response = Http::timeout(15)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude'        => $country->latitude,
                    'longitude'       => $country->longitude,
                    'current'         => 'temperature_2m,precipitation,wind_speed_10m,weather_code',
                    'forecast_days'   => 1,
                    'timezone'        => 'auto',
                ]);

                if (!$response->successful()) {
                    $failed++;
                    continue;
                }

                $data    = $response->json();
                $current = $data['current'] ?? null;

                if (!$current) {
                    $failed++;
                    continue;
                }

                // Hitung storm_risk dari weather_code Open-Meteo
                // WMO Weather Code: 95-99 = thunderstorm/storm
                $weatherCode = $current['weather_code'] ?? 0;
                $stormRisk   = $this->calculateStormRisk($weatherCode);

                WeatherData::updateOrCreate(
                    ['country_id' => $country->id],
                    [
                        'temperature' => $current['temperature_2m']    ?? null,
                        'rainfall'    => $current['precipitation']      ?? 0,
                        'wind_speed'  => $current['wind_speed_10m']     ?? null,
                        'storm_risk'  => $stormRisk,
                        'recorded_at' => now(),
                    ]
                );

                $success++;

            } catch (\Exception $e) {
                $failed++;
                continue;
            }
        }

        return back()->with('success', "✅ Fetch selesai! {$success} negara berhasil, {$failed} gagal.");
    }

    /**
     * Fetch data cuaca untuk satu negara tertentu
     */
    public function fetchOne(Country $country)
    {
        if (!$country->latitude || !$country->longitude) {
            return back()->with('error', "Negara {$country->country_name} tidak memiliki koordinat.");
        }

        try {
            $response = Http::timeout(15)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude'      => $country->latitude,
                'longitude'     => $country->longitude,
                'current'       => 'temperature_2m,precipitation,wind_speed_10m,weather_code',
                'forecast_days' => 1,
                'timezone'      => 'auto',
            ]);

            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data dari Open-Meteo API.');
            }

            $data    = $response->json();
            $current = $data['current'] ?? null;

            if (!$current) {
                return back()->with('error', 'Data cuaca tidak tersedia untuk negara ini.');
            }

            $weatherCode = $current['weather_code'] ?? 0;
            $stormRisk   = $this->calculateStormRisk($weatherCode);

            WeatherData::updateOrCreate(
                ['country_id' => $country->id],
                [
                    'temperature' => $current['temperature_2m']  ?? null,
                    'rainfall'    => $current['precipitation']    ?? 0,
                    'wind_speed'  => $current['wind_speed_10m']   ?? null,
                    'storm_risk'  => $stormRisk,
                    'recorded_at' => now(),
                ]
            );

            return back()->with('success', "✅ Data cuaca {$country->country_name} berhasil diperbarui.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Hitung storm risk (0-5) berdasarkan WMO Weather Code
     * 0   = Clear sky           → 0
     * 1-3 = Mainly clear/cloudy → 0
     * 45-48 = Fog               → 1
     * 51-67 = Drizzle/Rain      → 2
     * 71-77 = Snow              → 2
     * 80-82 = Rain showers      → 3
     * 85-86 = Snow showers      → 3
     * 95    = Thunderstorm      → 4
     * 96-99 = Heavy thunderstorm→ 5
     */
    private function calculateStormRisk(int $code): int
    {
        if ($code >= 96) return 5;
        if ($code === 95) return 4;
        if ($code >= 80) return 3;
        if ($code >= 51) return 2;
        if ($code >= 45) return 1;
        return 0;
    }
}
