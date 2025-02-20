<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceConfiguration extends Model
{
    use HasFactory;

    protected $table = 'price_configuration';

    protected $fillable = [
        'product_variant_id',
        'use_price_from', // 'import' or 'variant'
    ];

    // Quan hệ với ProductVariant
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getPrice($productVariantId)
{
    $priceConfig = $this->where('product_variant_id', $productVariantId)->first();
    
    if ($priceConfig && $priceConfig->use_price_from == 'import') {
        // Get price from latest import_details
        $importDetail = Import_detail::where('id_product_variant', $productVariantId)
            ->orderBy('created_at', 'desc')
            ->first();
        return $importDetail ? $importDetail->price_per_unit : null;
    } else {
        // Get price from product_variants
        $variant = Product_variant::find($productVariantId);
        return $variant ? $variant->price : null;
    }
}
}
