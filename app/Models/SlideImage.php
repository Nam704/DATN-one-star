<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SlideImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['slide_id', 'image', 'is_primary'];

    public function slide()
    {
        return $this->belongsTo(Slide::class);
    }

    protected $casts = [
        'is_primary' => 'boolean', // Chuyển đổi thành boolean khi lấy dữ liệu
    ];
}
