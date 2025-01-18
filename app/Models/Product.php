<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

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

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function variants()
    {
        return $this->hasMany(Product_variant::class, 'id_product');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'id_product');
    }
}
