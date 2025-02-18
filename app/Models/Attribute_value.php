<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute_value extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_attribute',
        'value',
    ];
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'id_attribute');
    }
    public function productVariants()
    {
        return $this->belongsToMany(Product_variant::class, 'product_variant_attributes', 'id_attribute_value', 'id_product_variant');
    }
    public static function findOrCreate($attributeName, $value)
    {
        $attribute = Attribute::firstOrCreate(['name' => $attributeName]);
        return self::firstOrCreate([
            'id_attribute' => $attribute->id,
            'value' => $value
        ]);
    }
}