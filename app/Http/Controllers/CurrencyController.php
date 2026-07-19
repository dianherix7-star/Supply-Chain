<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    /**
     * Tampilkan daftar exchange rates
     */
    public function index(Request $request)
    {
        $query = ExchangeRate::query();

        if ($request->filled('search')) {
            $query->where('currency', 'like', '%' . $request->search . '%');
        }

        $rates = $query->orderBy('currency')->paginate(20)->withQueryString();
        
        // Ambil semua rate untuk dropdown perbandingan/kalkulator
        $allRates = ExchangeRate::orderBy('currency')->get();

        // Statistik
        $stats = [
            'total_currencies' => ExchangeRate::count(),
            'last_updated'     => ExchangeRate::max('updated_at_api'),
            'strongest'        => ExchangeRate::where('exchange_rate', '>', 0)->orderBy('exchange_rate', 'asc')->first(),
            'weakest'          => ExchangeRate::orderBy('exchange_rate', 'desc')->first(),
        ];

        return view('currency.index', compact('rates', 'stats', 'allRates'));
    }

    /**
     * Fetch exchange rates dari Frankfurter API (gratis, tanpa API key)
     * Base currency: USD
     */
    public function fetchAll()
    {
        try {
            // Ambil semua currency codes dari countries yang sudah ada
            $currencies = Country::whereNotNull('currency')
                ->pluck('currency')
                ->flatMap(fn ($c) => array_filter(array_map(
                    fn ($code) => strtoupper(trim($code)),
                    preg_split('/\s*,\s*/', $c) ?: []
                )))
                ->unique()
                ->filter()
                ->values()
                ->toArray();

            if (empty($currencies)) {
                return back()->with('error', 'Tidak ada data mata uang di tabel countries. Import countries terlebih dahulu.');
            }

            // Frankfurter API — gratis, tanpa API key
            // Base: USD
            $response = Http::timeout(30)->get('https://api.frankfurter.dev/v1/latest', [
                'base' => 'USD',
            ]);

            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data dari Frankfurter API. Status: ' . $response->status());
            }

            $data = $response->json();
            $rates = $data['rates'] ?? [];

            if (empty($rates)) {
                return back()->with('error', 'Data exchange rate kosong dari API.');
            }

            $imported = 0;

            // Simpan USD sebagai base (rate = 1)
            ExchangeRate::updateOrCreate(
                ['currency' => 'USD'],
                [
                    'exchange_rate' => 1.0000,
                    'updated_at_api' => $data['date'] ?? now(),
                ]
            );
            $imported++;

            foreach ($rates as $currencyCode => $rate) {
                ExchangeRate::updateOrCreate(
                    ['currency' => $currencyCode],
                    [
                        'exchange_rate'  => $rate,
                        'updated_at_api' => $data['date'] ?? now(),
                    ]
                );
                $imported++;
            }

            return back()->with('success', "✅ Fetch selesai! {$imported} mata uang berhasil diperbarui (Base: USD).");

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
