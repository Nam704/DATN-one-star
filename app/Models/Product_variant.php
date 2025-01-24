<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attribute_value;

class Product_variant extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    public function attributes()
    {
        return $this->belongsToMany(
            Attribute_value::class,
            'product_variant_attributes',
            'id_product_variant',
            'id_attribute_value'
        );
    }
}
