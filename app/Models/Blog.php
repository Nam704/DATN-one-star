<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'blogs';
    protected $fillable = [
        'category_id', 'title', 'slug', 'content', 'thumbnail', 'status', 'published_at'
    ];

    public function category()
    {
        return $this->belongsTo(CategoryBlog::class, 'category_id')->withDefault();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'blog_id', 'tag_id')
                    ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(CommentBlog::class, 'blog_id');
    }
}
