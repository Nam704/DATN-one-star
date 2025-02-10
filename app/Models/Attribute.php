<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status'];

    public function values()
    {
        return $this->hasMany(Attribute_value::class, 'id_attribute');
    }
}
