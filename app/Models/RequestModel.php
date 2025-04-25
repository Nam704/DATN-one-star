<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RequestModel extends Model
{
    use HasFactory;

    protected $table = 'request_model';

    protected $fillable = [
        'employee_id',
        'action',
        'model_type',
        'model_id',
        'payload',
        'status',
        'admin_id',
        'approved_at',
    ];

    /**
     * Cast payload to array and approved_at to datetime
     */
    protected $casts = [
        'payload'     => 'array',
        'approved_at' => 'datetime',
    ];

    /**
     * The employee (user) who created the request.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * The admin (user) who approved or rejected the request.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Polymorphic relation to the target model
     */
    public function requestable(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }
}
