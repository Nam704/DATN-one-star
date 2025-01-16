<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_variant_attribute extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_prouct_variant',
        'id_attribute_value'
    ];
}
