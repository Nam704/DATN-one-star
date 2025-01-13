<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_variant extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'sku',
        'status',
    ];
    public function import_details()
    {
        return $this->hasMany(Import_detail::class, 'id_product_variant', 'id');
    }
    public static function scopeList($query, $idProduct)
    {
        return $query->select('product_variants.id', 'product_variants.sku', 'product_variants.status', 'products.name as product_name')
            ->join('products', 'product_variants.id_product', '=', 'products.id') // Thực hiện JOIN với bảng products
            ->where('id_product', '=', $idProduct)
            ->latest('product_variants.id'); // Sắp xếp theo id của product_variants
    }
    public static function scopeTotal($query, $id)
    {
        return $query->where('status', 'active')->where('id_product', $id);
    }
    public function variants()
{
    return $this->hasMany(Product_variant::class, 'product_id');
}

    public function images()
    {
        return $this->hasMany(Image::class, 'id_product_variant');
    }
    public function productAudits() {
        return $this->hasMany(Product_audit::class, 'id_product_variant');
    }
    public function attributes()
    {
        return $this->hasMany(Product_variant_attribute::class, 'id_product_variant');
    }

}
