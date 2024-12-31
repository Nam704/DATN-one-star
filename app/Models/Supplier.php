<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',

        'phone',
        'status',

    ];
    protected $dates = [
        'deleted_at'
    ];
    public static function scopeList($query)
    {
        return $query->select('id', 'name', 'phone', 'status')
            ->latest('id');
    }
    protected $hidden = ['created_at', 'updated_at'];

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
