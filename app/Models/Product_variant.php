<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product_variant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_variants';

    protected $fillable = [
        'id_product',
        'sku',
        'price',
        'price_sale',
        'quantity',
        'image',
        'status'
    ];

    // Scope lấy danh sách theo id sản phẩm
    public static function scopeList($query, $idProduct)
    {
        return $query->with('product')
                     ->where('id_product', '=', $idProduct)
                     ->latest('product_variants.id');
    }

    // Scope lấy tổng sản phẩm active theo id sản phẩm
    public static function scopeTotal($query, $id)
    {
        return $query->where('status', 'active')->where('id_product', $id);
    }

    // Scope lấy các sản phẩm giảm giá
    public function scopeOnSale($query)
    {
        return $query->where('price_sale', '>', 0)
                     ->whereColumn('price_sale', '<', 'price')
                     ->where('status', 'active');
    }

    // Accessor tính phần trăm giảm giá
    public function getDiscountPercentAttribute()
    {
        if ($this->price_sale && $this->price > 0) {
            return round((($this->price - $this->price_sale) / $this->price) * 100);
        }
        return 0;
    }

    // Accessor kiểm tra sản phẩm có giảm giá không
    public function getIsOnSaleAttribute()
    {
        return $this->price_sale > 0 && $this->price > $this->price_sale;
    }

    // Quan hệ với model Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    // Quan hệ với model Image
    public function images()
    {
        return $this->hasMany(Image::class, 'id_product_variant');
    }

    // Các quan hệ khác
    public function import_details()
    {
        return $this->hasMany(Import_detail::class, 'id_product_variant', 'id');
    }

    public function productAudits()
    {
        return $this->hasMany(Product_audit::class, 'id_product_variant');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'id_product_variant');
    }
}
