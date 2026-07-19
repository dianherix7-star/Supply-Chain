<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CountryDetailController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\RiskScoreController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\EconomicDataController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ArticleController;

Route::get('/', function () {
    return redirect('/login');
});

// Authentication (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Protected routes — harus login
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('admin')
        ->name('admin.dashboard');

    // User Dashboard
    Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');

    // Read-only / User accessible views
    Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
    Route::get('/countries/{country}', [CountryDetailController::class, 'show'])->name('countries.show');

    Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
    Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.index');
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/economic', [EconomicDataController::class, 'index'])->name('economic.index');
    Route::get('/risk', [RiskScoreController::class, 'index'])->name('risk.index');
    Route::get('/ports', [PortController::class, 'index'])->name('ports.index');
    Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
    Route::get('/compare/result', [CompareController::class, 'result'])->name('compare.result');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

    // Watchlist (Personal to each user)
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::post('/watchlist/add', [WatchlistController::class, 'add'])->name('watchlist.add');
    Route::delete('/watchlist/remove/{country}', [WatchlistController::class, 'remove'])->name('watchlist.remove');

    // ==========================================
    // Admin-only routes (Mutating & API fetch)
    // ==========================================
    Route::middleware('admin')->group(function () {
        // Admin Dashboard
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

        // Country CRUD (Admin only)
        Route::get('/countries/import', [CountryController::class, 'import'])->name('countries.import');
        Route::get('/countries/create', [CountryController::class, 'create'])->name('countries.create');
        Route::post('/countries', [CountryController::class, 'store'])->name('countries.store');
        Route::get('/countries/{country}/edit', [CountryController::class, 'edit'])->name('countries.edit');
        Route::put('/countries/{country}', [CountryController::class, 'update'])->name('countries.update');
        Route::delete('/countries/{country}', [CountryController::class, 'destroy'])->name('countries.destroy');

        // Ports CRUD & Fetch (Admin only)
        Route::get('/ports/create', [PortController::class, 'create'])->name('ports.create');
        Route::post('/ports', [PortController::class, 'store'])->name('ports.store');
        Route::get('/ports/{port}/edit', [PortController::class, 'edit'])->name('ports.edit');
        Route::put('/ports/{port}', [PortController::class, 'update'])->name('ports.update');
        Route::delete('/ports/{port}', [PortController::class, 'destroy'])->name('ports.destroy');
        Route::post('/ports/fetch-osm', [PortController::class, 'fetchFromOSM'])->name('ports.fetch-osm');
        Route::post('/ports/fetch-osm/{country}', [PortController::class, 'fetchByCountry'])->name('ports.fetch-country');

        // Weather Fetch (Admin only)
        Route::post('/weather/fetch-all', [WeatherController::class, 'fetchAll'])->name('weather.fetch-all');
        Route::post('/weather/fetch/{country}', [WeatherController::class, 'fetchOne'])->name('weather.fetch-one');

        // Currency Fetch (Admin only)
        Route::post('/currency/fetch-all', [CurrencyController::class, 'fetchAll'])->name('currency.fetch-all');

        // News Fetch (Admin only)
        Route::post('/news/fetch-all', [NewsController::class, 'fetchAll'])->name('news.fetch-all');
        Route::post('/news/fetch/{country}', [NewsController::class, 'fetchByCountry'])->name('news.fetch-country');

        // Economic Fetch (Admin only)
        Route::post('/economic/fetch-all', [EconomicDataController::class, 'fetchAll'])->name('economic.fetch-all');
        Route::post('/economic/fetch/{country}', [EconomicDataController::class, 'fetchOne'])->name('economic.fetch-one');

        // Risk Calculation (Admin only)
        Route::post('/risk/calculate-all', [RiskScoreController::class, 'calculateAll'])->name('risk.calculate-all');
        Route::post('/risk/calculate/{country}', [RiskScoreController::class, 'calculateOne'])->name('risk.calculate-one');

        // Articles CRUD (Admin only)
        Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
        Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    });
});
