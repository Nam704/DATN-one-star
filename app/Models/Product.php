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
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function Category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }


    public function Brand()
    {
        return $this->belongsTo(Brand::class, 'id_brand');
    }



    public static function scopeList($query)
    {
        return $query->select('id', 'name', 'status')
            ->latest('id');
    }
    public static function scopeTotal($query)
    {
        return $query->where('status', 'active');
    }
    public function variants()
    {
        return $this->hasMany(Product_variant::class, 'id_product');
    }
    public function product_albums()
    {
        return $this->hasMany(Product_albums::class, 'id_product');
    }
}
