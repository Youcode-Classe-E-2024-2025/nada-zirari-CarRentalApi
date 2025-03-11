<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'year',
        'color',
        'daily_rate',
        'is_available'
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
