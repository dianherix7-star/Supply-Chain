<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    protected $fillable = [
        'country_id',
        'weather_score',
        'inflation_score',
        'currency_score',
        'news_score',
        'total_score',
        'risk_level',
    ];

    protected function casts(): array
    {
        return [
            'weather_score'   => 'integer',
            'inflation_score' => 'integer',
            'currency_score'  => 'integer',
            'news_score'      => 'integer',
            'total_score'     => 'integer',
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}