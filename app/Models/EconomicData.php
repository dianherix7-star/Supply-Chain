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
        'year',
    ];

    protected function casts(): array
    {
        return [
            'gdp'        => 'float',
            'inflation'  => 'float',
            'population' => 'float',
            'exports'    => 'float',
            'imports'    => 'float',
            'year'       => 'integer',
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}