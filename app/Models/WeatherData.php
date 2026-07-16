<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherData extends Model
{
    protected $fillable = [
        'country_id',
        'temperature',
        'rainfall',
        'wind_speed',
        'storm_risk',
        'recorded_at'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}