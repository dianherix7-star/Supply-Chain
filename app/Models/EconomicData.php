<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EconomicData extends Model
{
    protected $fillable = [
        'country_id',
        'gdp',
        'inflation',
        'population',
        'exports',
        'imports',
        'year'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}