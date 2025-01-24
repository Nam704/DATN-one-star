<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product_variant;

class Product extends Model
{
    use HasFactory;

    public function variants()
    {
        return $this->hasMany(Product_variant::class, 'id_product');
    }
}
