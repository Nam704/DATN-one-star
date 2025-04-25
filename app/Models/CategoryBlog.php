<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryBlog extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'category_blog';
    protected $fillable = ['name', 'slug'];
    protected $dates = ['deleted_at']; 


    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
