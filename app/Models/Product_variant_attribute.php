<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_variant_attribute extends Model
{
    use HasFactory;
    protected $table = 'product_variant_attributes';
    protected $fillable = [
        'id_product_variant',
        'id_attribute',
        'id_attribute_value',
    ];
}
