<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart_details extends Model
{
    use HasFactory;
    protected $fillable = ['id_cart', 'id_variant', 'quantity', 'price', 'total_price'];
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'id_cart');
    }
    public function variant()
    {
        return $this->belongsTo(Product_variant::class, 'id_variant');
    }
}
