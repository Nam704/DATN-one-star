<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCancellationReason extends Model
{
    use HasFactory;
    protected $table = 'order_cancellation_reasons';
    protected $fillable = ['reason'];
    public function orderCancellations()
    {
        return $this->hasMany(OrderCancellation::class, 'reason_id');
    }
}