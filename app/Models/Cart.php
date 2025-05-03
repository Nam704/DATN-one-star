<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['id_user'];
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function cartItems()
    {
        return $this->hasMany(Cart_details::class, 'id_cart');
    }
    public function details()
    {
        return $this->hasMany(Cart_details::class, 'id_cart');
    }
}
