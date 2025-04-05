<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderExpire extends Model
{
    use HasFactory;
    protected $table = 'order_expires';
    protected $fillable = ['id_order', 'expires_at'];
    function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }
}
