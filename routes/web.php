<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\WeatherController;

Route::get('/', function () {
    return redirect('/login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');

// Import Country API
Route::get('/countries/import', [CountryController::class, 'import'])
    ->name('countries.import');
// CRUD Country
Route::resource('countries', CountryController::class)
    ->except(['show']);

// ===== Tahap 4: Weather API =====
Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
Route::post('/weather/fetch-all', [WeatherController::class, 'fetchAll'])->name('weather.fetch-all');
Route::post('/weather/fetch/{country}', [WeatherController::class, 'fetchOne'])->name('weather.fetch-one');