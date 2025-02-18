<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    
    protected $fillable = [
        'name',
        'id_parent',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'id_parent');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'id_parent');
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('id_parent');
    }

    public function products() {
        return $this->hasMany(Product::class, 'id_category');
    }
}