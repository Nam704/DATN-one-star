<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'order_id',
        'txn_ref',
        'amount',
        'status',
        'payment_method',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
