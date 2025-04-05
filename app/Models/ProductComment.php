<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductComment extends Model
{
    use HasFactory;

    protected $table = 'comments_product';

    protected $fillable = [
        'product_id',
        'user_id',
        'comment',
        'parent_id',
    ];

    // Định nghĩa các quan hệ
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentComment()
    {
        return $this->belongsTo(ProductComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ProductComment::class, 'parent_id');
    }
}
