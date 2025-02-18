<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_variant_attribute extends Model
{
    use HasFactory;

    protected $table = "product_variant_attributes";
    protected $fillable = [
        'id_product_variant',
        'id_attribute_value',
        'created_at',
        'updated_at',
    ];
    // Quan hệ với Variant
    public function variant()
    {
        return $this->belongsTo(Product_variant::class, 'id_product_variant');
    }

    // Quan hệ với AttributeValue
    public function attributeValue()
    {
        return $this->belongsTo(Attribute_value::class, 'id_attribute_value');
    }
}
