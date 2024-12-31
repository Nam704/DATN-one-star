<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Import extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'id_supplier',
        'import_date',
        'total_amount',
        'note',

    ];
}
