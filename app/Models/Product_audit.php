<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_audit extends Model
{
    use HasFactory;

    protected $table = 'product_audits';
    

    protected $fillable = [
        'id_user',
        'id_product_variant',
        'quantity',
        'action_type',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }


    public function productVariant()
    {
        return $this->belongsTo(Product_variant::class, 'id_product_variant');
    }
}
