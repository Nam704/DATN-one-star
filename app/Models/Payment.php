<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'txn_ref',
        'amount',
        'bank_code',
        'transaction_no',
        'card_type',
        'response_code',
        'pay_date',
    ];

}
