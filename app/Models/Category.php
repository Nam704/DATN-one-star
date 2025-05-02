<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'id_parent',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'id_parent');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'id_parent');
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('id_parent');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'id_category');
    }
    public function getPriceRange()
    {
        return $this->products()
            ->join('product_variants', 'products.id', '=', 'product_variants.id_product')
            ->where('products.id_category', $this->id)  // Lọc theo id_category của category hiện tại
            ->whereNull('products.deleted_at')  // Kiểm tra xem sản phẩm có bị xóa mềm không
            ->selectRaw('MIN(product_variants.price) as min_price, MAX(product_variants.price) as max_price')
            ->first();  // Lấy một kết quả duy nhất vì chỉ có một min và max giá cho mỗi category
    }

    // public static function categories_with_revenue()
    // {
    //     return self::select('categories.id', 'categories.name')
    //         ->leftJoin('products', 'categories.id', '=', 'products.id_category')
    //         ->leftJoin('product_variants', 'products.id', '=', 'product_variants.id_product')
    //         ->leftJoin('order_details', 'product_variants.id', '=', 'order_details.id_variant')
    //         ->leftJoin('orders', function ($join) {
    //             $join->on('order_details.id_order', '=', 'orders.id')
    //                 ->where('orders.id_order_status', '=', 4);
    //         })
    //         ->whereNull('categories.deleted_at')
    //         ->whereNull('products.deleted_at')
    //         ->orWhereNull('products.id')
    //         ->groupBy('categories.id', 'categories.name')
    //         ->selectRaw('COALESCE(SUM(order_details.quantity * order_details.unit_price), 0) as total_revenue')
    //         ->orderBy('total_revenue', 'desc')
    //         ->get();
    // }
    public static function categories_with_revenue($start_date, $end_date)
    {
        return self::select('categories.id', 'categories.name')
            ->leftJoin('products', 'categories.id', '=', 'products.id_category')
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.id_product')
            ->leftJoin('order_details', 'product_variants.id', '=', 'order_details.id_variant')
            ->leftJoin('orders', 'order_details.id_order', '=', 'orders.id')
            ->leftJoin('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
            ->whereNull('categories.deleted_at')
            ->whereNull('products.deleted_at')
            ->groupBy('categories.id', 'categories.name')
            ->selectRaw('
                -- Tổng doanh thu từ các đơn hàng hợp lệ
                COALESCE(SUM(
                    CASE
                        WHEN order_statuses.name NOT IN ("Cancelled", "Cancel Requested", "Cancel Under Review", "Cancel Approved", "Cancel Rejected", "Failed Delivery")
                             AND orders.created_at BETWEEN ? AND ?
                        THEN order_details.quantity * order_details.unit_price
                        ELSE 0
                    END
                ), 0) as total_revenue,
    
                -- Tổng số sản phẩm được tạo trong khoảng thời gian
                COUNT(DISTINCT
                    CASE
                        WHEN products.created_at BETWEEN ? AND ?
                        THEN products.id
                        ELSE NULL
                    END
                ) as total_products
            ', [$start_date, $end_date, $start_date, $end_date])
            ->orderByDesc('total_revenue')
            ->get();
    }
    

}

