<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRestriction extends Model
{
    protected $fillable = [
        'user_id',
        'restriction_type',
        'restricted_at',
        'expires_at',
    ];

    protected $casts = [
        'restricted_at' => 'datetime',
        'expires_at' => 'datetime',
        'restriction_type' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
