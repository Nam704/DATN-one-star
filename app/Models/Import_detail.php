<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import_detail extends Model
{
    use HasFactory;
    protected $table = 'import_details';
    protected $fillable = [
        'id_import', 'id_product_variant', 'quantity', 'price_per_unit', 'expected_price', 'total_price'
    ];
    public function product_variant()
    {
        return $this->belongsTo(Product_variant::class, 'id_product_variant', 'id');
    }
    public function import()
    {
        return $this->belongsTo(Import::class, 'id_import', 'id');
    }
}
