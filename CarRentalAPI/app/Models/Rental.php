<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $fillable = [
        'car_id',
        'customer_name',
        'start_date',
        'end_date',
        'total_cost'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
