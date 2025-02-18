<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{

    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'id_brand',
        'id_category',
        'description',
        'product_type',
        'image_primary',
        'status',
        'price',
        'price_sale',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function Category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
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
    public function listActive()
    {
        return self::with([
            'Category:id,name,status',
            'Brand:id,name,status',
            'variants:id_product,quantity,price'
        ])
            ->addSelect([
                'total_quantity' => Product_variant::selectRaw('SUM(quantity)')
                    ->whereColumn('id_product', 'products.id'),
                'min_price' => Product_variant::selectRaw('MIN(price)')
                    ->whereColumn('id_product', 'products.id'),
                'max_price' => Product_variant::selectRaw('MAX(price)')
                    ->whereColumn('id_product', 'products.id')
            ])
            ->orderBy('id', 'desc');
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
    public static function getProductWithDetails($id)
    {
        return self::with([
            'variants' => function ($query) {
                $query->select('id', 'id_product', 'sku', 'status', 'quantity', 'price')
                    ->with([
                        'images' => function ($query) {
                            $query->select('id', 'id_product_variant', 'url');
                        },
                        'attributeValues' => function ($query) {
                            $query->select('attribute_values.id', 'attribute_values.value')
                                ->join('attributes', 'attribute_values.id_attribute', '=', 'attributes.id')
                                ->addSelect('attributes.name as name', 'attributes.id as attribute_id',);
                        }
                    ]);
            },
            'category:id,name',
            'brand:id,name',
            'product_albums:id,id_product,image_path',
        ])->select('id', 'name', 'id_brand', 'id_category', 'description', 'image_primary', 'status',);
    }
}