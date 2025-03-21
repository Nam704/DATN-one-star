<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_status extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    protected $table = 'order_statuses';
    public function orders()
    {
        return $this->hasMany(Order::class, 'id_order_status');
    }
    public function nextStatus()
    {
        return $this->belongsTo(Order_status::class, 'next_status_id');
    }
}
