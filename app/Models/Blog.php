<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blogs';

    protected $dates = ['deleted_at'];
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

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value;

        if ($value === 'published' && empty($this->attributes['published_at'])) {
            $this->attributes['published_at'] = Carbon::now();
        }
    }
}
