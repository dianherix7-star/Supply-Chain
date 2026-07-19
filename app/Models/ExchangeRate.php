<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'currency',
        'exchange_rate',
        'updated_at_api',
    ];

    protected function casts(): array
    {
        return [
            'exchange_rate'  => 'float',
            'updated_at_api' => 'datetime',
        ];
    }
}