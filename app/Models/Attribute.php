<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status'
    ];
    public function values()
    {
        return $this->hasMany(Attribute_value::class, 'id_attribute');
    }
    public static function findOrCreate($name)
    {
        return self::firstOrCreate(['name' => $name]);
    }
}