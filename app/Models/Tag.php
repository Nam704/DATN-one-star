<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tag';
    protected $fillable = ['name', 'slug'];

    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'post_tag', 'tag_id', 'blog_id')
            ->withTimestamps();
    }
}
