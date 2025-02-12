<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'status',
        "id_import"
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function import()
    {
        return $this->belongsTo(Import::class, 'id_import');
    }

    public function productVariant()
    {
        return $this->belongsTo(Product_variant::class, 'id_product_variant');
    }
    public function product()
    {
        return $this->hasOneThrough(
            Product::class,       // Model đích: Product (bảng products)
            Product_variant::class, // Model trung gian: ProductVariant (bảng product_variants)
            'id',                  // foreignKeyOnB: Khóa chính của bảng product_variants (id_product_variant)
            'id',                  // foreignKeyOnC: Khóa chính của bảng products (id_product)
            'id_product_variant',  // localKeyOnA: Khóa ngoại trong bảng product_audits (id_product_variant)
            'id_product'           // localKeyOnB: Khóa ngoại trong bảng product_variants (id_product)
        );
    }
    public function scopeWithProductAndUserSummary($query)
    {
        return $query
            ->join('product_variants', 'product_audits.id_product_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->join('users', 'product_audits.id_user', '=', 'users.id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'users.id as user_id',
                'users.name as user_name',
                'product_audits.action_type',
                'product_audits.status',
                DB::raw('SUM(product_audits.quantity) as total_quantity')
            )
            ->where('product_audits.status', 'approved')
            ->groupBy(
                'products.id',
                'products.name',
                'users.id',
                'users.name',
                'product_audits.action_type',
                'product_audits.status'
            );
    }
    public function scopeWithProductAndUserSummarPending($query)
    {
        return $query
            ->join('product_variants', 'product_audits.id_product_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->join('users', 'product_audits.id_user', '=', 'users.id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'users.id as user_id',
                'users.name as user_name',
                'product_audits.action_type',
                'product_audits.status',
                DB::raw('SUM(product_audits.quantity) as total_quantity')
            )
            ->where('product_audits.status', 'pending')
            ->groupBy(
                'products.id',
                'products.name',
                'users.id',
                'users.name',
                'product_audits.action_type',
                'product_audits.status'
            );
    }
}
