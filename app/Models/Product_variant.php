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
        'status',
    ];

    public function images()
    {
        return $this->hasMany(Image::class, 'id_product_variant');
    }

    public function productAudits() {
        return $this->hasMany(Product_audit::class, 'id_product_variant');
    }
}
