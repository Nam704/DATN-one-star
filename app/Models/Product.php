<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'id_brand',
        'id_category',
        'description',
        'image_primary',
        'status'
    ];


    public function brand()
    {
        return $this->belongsTo(Brand::class, 'id_brand');
    }
    public function images()
    {
        return $this->hasManyThrough(Image::class, Product_variant::class, 'id_product', 'id_product_variant');
    }

    public function variants()
    {
        return $this->hasMany(Product_variant::class, 'id_product');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

}
