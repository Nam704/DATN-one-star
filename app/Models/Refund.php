<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\RefundStatus;

class Refund extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'user_id',
        'bank_details',
        'status'
    ];
    protected $casts = [
        'bank_details' => 'array',
        'status' => RefundStatus::class,
    ];

    // Quan hệ với Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Quan hệ với User (người yêu cầu)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope cho các trạng thái
    public function scopePending($query)
    {
        return $query->where('status', RefundStatus::PENDING);
    }
}
