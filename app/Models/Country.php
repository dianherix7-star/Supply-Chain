<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'country_code',
        'country_name',
        'capital',
        'region',
        'population',
        'currency',
        'flag',
        'latitude',
        'longitude',
    ];
}