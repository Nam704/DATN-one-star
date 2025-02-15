<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_detail extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_order', 'id_product_variant', 'quantity', 'unit_price', 'total_price', 'product_name', 'id_user'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function productVariant()
    {
        return $this->belongsTo(Product_variant::class, 'id_product_variant');
    }
}
