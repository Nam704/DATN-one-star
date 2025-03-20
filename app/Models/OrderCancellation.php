<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    use HasFactory;
    protected $table = 'order_cancellations';
    protected $fillable = ['order_id', 'reason_id', 'status'];

    // Quan hệ với bảng order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Quan hệ với bảng lý do hủy
    public function reason()
    {
        return $this->belongsTo(OrderCancellationReason::class, 'reason_id');
    }
}