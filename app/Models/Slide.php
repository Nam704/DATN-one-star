<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slide extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['title', 'description', 'category_id', 'is_active','display_locations'];

    protected $casts = [
        'display_locations' => 'array', // Chuyển dữ liệu JSON thành mảng PHP
    ];

    public function images()
    {
        return $this->hasMany(SlideImage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(SlideImage::class)->where('is_primary', 1);
    }

    public function secondaryImages()
    {
        return $this->hasMany(SlideImage::class)->where('is_primary', 0);
    }


}
