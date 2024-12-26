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
    ];
    protected $dates = [
        'deleted_at'
    ];
    public function list()
    {
        return $suppliers = $this->query()->select('id', 'name', 'address', 'phone', 'status')->latest('id')->paginate(10);
    }
}
