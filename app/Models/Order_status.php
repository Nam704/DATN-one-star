<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_status extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_order_status');
    }
}
