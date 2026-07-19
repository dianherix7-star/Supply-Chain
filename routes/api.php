<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes — Supply Chain Global
|--------------------------------------------------------------------------
| GET /api/countries  — Daftar negara + risk score
| GET /api/risk       — Risk score semua negara
| GET /api/ports      — Daftar pelabuhan
| GET /api/news       — Berita + sentiment
| GET /api/currency   — Kurs mata uang
| GET /api/weather    — Data cuaca
| GET /api/economic   — Data ekonomi (GDP, inflasi, dll)
*/

// Public API endpoints (tidak butuh autentikasi)
Route::get('/countries', [ApiController::class, 'countries']);
Route::get('/risk',      [ApiController::class, 'risk']);
Route::get('/ports',     [ApiController::class, 'ports']);
Route::get('/news',      [ApiController::class, 'news']);
Route::get('/currency',  [ApiController::class, 'currency']);
Route::get('/weather',   [ApiController::class, 'weather']);
Route::get('/economic',  [ApiController::class, 'economic']);

// API Info
Route::get('/', function () {
    return response()->json([
        'name'    => 'Supply Chain Risk Intelligence API',
        'version' => '1.0',
        'endpoints' => [
            'GET /api/countries' => 'Daftar negara (param: search, region, per_page)',
            'GET /api/risk'      => 'Risk scores (param: level=High|Medium|Low)',
            'GET /api/ports'     => 'Pelabuhan (param: search, country_id, per_page)',
            'GET /api/news'      => 'Berita (param: sentiment=Positive|Negative|Neutral, country_id)',
            'GET /api/currency'  => 'Kurs mata uang vs USD',
            'GET /api/weather'   => 'Data cuaca (param: storm_min=0-5)',
            'GET /api/economic'  => 'Data ekonomi (param: country_id)',
        ]
    ]);
});
