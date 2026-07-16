<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'country_id',
        'title',
        'description',
        'url',
        'source',
        'sentiment',
        'published_at'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}