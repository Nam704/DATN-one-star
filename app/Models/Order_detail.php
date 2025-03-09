<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_detail extends Model
{
    use HasFactory;
    protected $table = 'order_details';
    protected $fillable = ['id_order', 'id_product_variant', 'quantity', 'unit_price', 'total_price','product_name','id_user'];
    public function product_variant()
    {
        return $this->belongsTo(Product_variant::class, 'id_product_variant');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }
}
