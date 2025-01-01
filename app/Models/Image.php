<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status',
        'url',
        'id_product_variant',
    ];

    public function productVariant()
    {
        return $this->belongsTo(Product_variant::class, 'id_product_variant');
    }
}
