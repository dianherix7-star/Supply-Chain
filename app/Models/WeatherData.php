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
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'temperature' => 'float',
            'rainfall'    => 'float',
            'wind_speed'  => 'float',
            'storm_risk'  => 'integer',
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}