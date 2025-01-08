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

    public function Category(){
        return $this->belongsTo(Category::class,'id_category');
    }

    public function Brand(){
        return $this->belongsTo(Brand::class,'id_brand');
    }

    //     public static function scopeList($query, $idProduct)
    // {
    //     return $query->select('suppliers.id', 'suppliers.name', 'suppliers.status', 'products.name as product_name')  // Thêm cột 'product_name' từ bảng 'products'
    //         ->join('products', 'suppliers.product_id', '=', 'products.id')  // Thực hiện JOIN giữa 'suppliers' và 'products'
    //         ->where('products.id', $idProduct)  // Lọc theo 'idProduct'
    //         ->latest('suppliers.id');  // Sắp xếp theo 'id' của bảng 'suppliers'
    // }
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
}
