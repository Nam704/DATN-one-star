<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'status',
    ];
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
}