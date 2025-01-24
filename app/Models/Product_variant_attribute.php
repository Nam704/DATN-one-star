<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Product_variant_attribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_variant_attributes';

    protected $fillable = [
        'id_product_variant',
        'id_attribute_value',
    ];

    // Thuộc một biến thể sản phẩm
    public function productVariant()
    {
        return $this->belongsTo(Product_variant::class, 'id_product_variant');
    }

    // Thuộc một giá trị thuộc tính
    public function attributeValue()
    {
        return $this->belongsTo(Attribute_value::class, 'id_attribute_value');
    }

    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            Product_variant::class,
            'id', // Khóa chính của bảng `product_variants`
            'id', // Khóa chính của bảng `products`
            'id_product_variant', // Khóa ngoại trong `product_variant_attributes` (tới `product_variants`)
            'id_product' // Khóa ngoại trong `product_variants` (tới `products`)
        );
    }
}
