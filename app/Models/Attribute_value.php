<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute_value extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'id_attribute',
        'value',
        'status'
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'id_attribute');
    }
}
