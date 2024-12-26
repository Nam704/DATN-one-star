<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'phone',
        'status',
        'id'
    ];
    protected $dates = [
        'deleted_at'
    ];
    public function list()
    {
        return $suppliers = $this->query()->select('id', 'name', 'address', 'phone', 'status')->latest('id')->paginate(10);
    }
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable'); // Định nghĩa quan hệ với Address
    }
}
