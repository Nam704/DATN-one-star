<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_albums extends Model
{
    use HasFactory;
    protected $table = 'product_albums';
    protected $fillable = [
        'id_product',
        'image_path',
        'created_at',
        'updated_at',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
