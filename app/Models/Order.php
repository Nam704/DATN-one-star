<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_user', 'phone_number', 'address', 'total_amount', 'id_order_status', 'id_voucher'
    ];

    // Quan hệ với bảng trạng thái đơn hàng
    public function orderStatus()
    {
        return $this->belongsTo(Order_status::class, 'id_order_status');
    }

    // Quan hệ với bảng chi tiết đơn hàng
    public function orderDetails()
    {
        return $this->hasMany(Order_detail::class, 'id_order');
    }

    // Quan hệ với bảng voucher (nếu có)
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'id_voucher');
    }

    public function getComputedTotalAttribute()
    {
        return $this->orderDetails->sum(function($detail) {
            return $detail->quantity * $detail->unit_price;
        });
    }
}
