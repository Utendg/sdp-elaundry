<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'is_approved',
        'is_available',
        'approved_at',
        'approved_by',
        'rating_avg',
        'ratings_count',
        'orders_completed',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_available' => 'boolean',
        'approved_at' => 'datetime',
        'rating_avg' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
