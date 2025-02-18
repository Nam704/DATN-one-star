<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'discount_amount',
        'quantity',
        'start_date',
        'end_date',
        'min_amount'
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
