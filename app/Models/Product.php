<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'id_brand', 'id_category', 'description', 'image_primary', 'status'
    ];

    public function variants()
    {
        return $this->hasMany(Product_variant::class, 'id_product');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'id_brand');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function images()
{
    return $this->hasManyThrough(
        Image::class,
        Product_variant::class,
        'product_id', // Khóa ngoại trong bảng `product_variants`
        'id_product_variant', // Khóa ngoại trong bảng `images`
        'id', // Khóa chính trong bảng `products`
        'id' // Khóa chính trong bảng `product_variants`
    );
}
}
