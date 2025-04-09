<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'description',
        'discount_amount',
        'quantity',
        'start_date',
        'end_date',
        'min_amount',
        'max_discount_amount',
        'type',
        'user_limit',
        'total_usage',
        'status',
        'applies_to',
    ];

    // Cast applies_to thành mảng
    protected $casts = [
        'applies_to' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_voucher');
    }
}
