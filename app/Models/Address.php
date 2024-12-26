<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['address_detail', 'is_default', 'id_ward'];

    // Quan hệ polymorphic
    public function addressable()
    {
        return $this->morphTo(); // Định nghĩa quan hệ polymorphic
    }
}
