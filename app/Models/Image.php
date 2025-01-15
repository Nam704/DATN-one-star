<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [ 'url', 'id_product_variant','status'];

    public function product_variant()
    {
        return $this->belongsTo(Product_variant::class, 'id_product_variant');
    }

}
