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
        'address',
        'phone',
        'status',

    ];
    protected $dates = [
        'deleted_at'
    ];
    public function scopeList($query)
    {
        return $query->select('id', 'name', 'address', 'phone', 'status')
            ->latest('id')
            ->paginate(10);
    }
    protected $hidden = ['created_at', 'updated_at'];

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
