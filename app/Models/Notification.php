<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'notifications';

    protected $fillable = [
        'type',
        'title',
        'message',
        'from_user_id',
        'to_user_id',
        'status',
        'read_at',
        'goto_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
}
