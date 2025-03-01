<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $fillable = ['id', 'id_user', 'phone_number', 'address', 'total_amount'];

    public function orderStatus()
    {
        return $this->belongsTo(Order_status::class, 'id_order_status');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function orderDetails()
    {
        return $this->hasMany(Order_detail::class, 'id_order');
    }
}
