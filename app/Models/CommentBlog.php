<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentBlog extends Model
{
    use HasFactory;

    protected $table = 'comment_blog';
    protected $fillable = ['blog_id', 'user_name', 'email', 'content'];

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }
}
