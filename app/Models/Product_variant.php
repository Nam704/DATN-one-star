<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product_variant extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'product_variants';
    protected $fillable  = [
        'id_product',
        'sku',
        'status',
        'quantity',
        'price',
        'price_sale',
    ];
    public function import_details()
    {
        return $this->hasMany(Import_detail::class, 'id_product_variant', 'id');
    }
    public static function scopeList($query, $idProduct)
    {
        return $query->select('product_variants.id', 'product_variants.sku', 'product_variants.status', 'product_variants.quantity', 'products.name as product_name')
            ->join('products', 'product_variants.id_product', '=', 'products.id') // Thực hiện JOIN với bảng products
            ->where('id_product', '=', $idProduct)
            ->latest('product_variants.id'); // Sắp xếp theo id của product_variants
    }
    public static function scopeTotal($query, $id)
    {
        return $query->where('status', 'active')->where('id_product', $id);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    public function images()
    {
        return $this->hasOne(Image::class, 'id_product_variant');
    }

    public function productAudits()
    {
        return $this->hasMany(Product_audit::class, 'id_product_variant');
    }
    public function attributeValues()
    {
        return $this->belongsToMany(Attribute_value::class, 'product_variant_attributes', 'id_product_variant', 'id_attribute_value');
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

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'id_product_variant');
    }
    public function getCurrentPrice()
{
    $priceConfig = new PriceConfiguration();
    return $priceConfig->getPrice($this->id);
}


}


