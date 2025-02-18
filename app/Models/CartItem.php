<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['id_cart', 'id_product_variant', 'quantity', 'price'];

    // Liên kết với bảng Cart (1 sản phẩm thuộc về 1 giỏ hàng)
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'id_cart');
    }

    // Liên kết với bảng ProductVariant (1 sản phẩm trong giỏ thuộc về 1 biến thể sản phẩm)
    public function productVariant()
{
    return $this->belongsTo(Product_variant::class, 'id_product_variant');
}

}
