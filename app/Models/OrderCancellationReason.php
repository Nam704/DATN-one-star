<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCancellationReason extends Model
{
    use HasFactory;
    protected $table = 'order_cancellation_reasons';
    protected $fillable = ['reason', 'to'];
    public function forClient()
    {
        return $this->where('to', 'client');
    }
    public function forAdmin()
    {
        return $this->where('to', 'admin');
    }
    public function orderCancellations()
    {
        return $this->hasMany(OrderCancellation::class, 'reason_id');
    }
}
