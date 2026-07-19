<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'country_code',
        'country_name',
        'capital',
        'region',
        'subregion',
        'population',
        'currency',
        'flag',
        'latitude',
        'longitude',
    ];

    /**
     * Accessor: URL gambar bendera PNG dari flagcdn.com
     * Contoh: $country->flag_url => 'https://flagcdn.com/w40/id.png'
     */
    protected function flagUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->country_code
                ? 'https://flagcdn.com/w40/' . strtolower($this->country_code) . '.png'
                : null,
        );
    }

    /**
     * Accessor: Emoji bendera dari kolom flag di DB
     * Contoh: $country->flag_emoji => '🇮🇩'
     */
    protected function flagEmoji(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->attributes['flag'] ?? null,
        );
    }

    // Relasi ke weather_data (data cuaca terbaru)
    public function weatherData()
    {
        return $this->hasOne(WeatherData::class)->latest('recorded_at');
    }

    // Relasi ke news
    public function news()
    {
        return $this->hasMany(News::class);
    }

    // Relasi ke risk_scores (skor terbaru)
    public function riskScore()
    {
        return $this->hasOne(RiskScore::class)->latest('updated_at');
    }

    // Relasi ke economic_data
    public function economicData()
    {
        return $this->hasMany(EconomicData::class);
    }

    // Relasi ke ports
    public function ports()
    {
        return $this->hasMany(Port::class);
    }

    // Relasi ke watchlist
    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }
}