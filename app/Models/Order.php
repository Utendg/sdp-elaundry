<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    /** Ordered list of the "in progress" statuses used by the tracking timeline. */
    public const PIPELINE = ['pending', 'accepted', 'picked_up', 'washing', 'ironing', 'ready', 'completed'];

    protected $fillable = [
        'reference',
        'student_id',
        'worker_id',
        'dorm_id',
        'status',
        'total_price',
        'notes',
        'pickup_location',
        'scheduled_pickup_at',
        'accepted_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'scheduled_pickup_at' => 'datetime',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Assign a unique human-friendly reference when creating an order.
        static::creating(function (Order $order) {
            if (empty($order->reference)) {
                $order->reference = 'ELD-'.strtoupper(Str::random(6));
            }
        });
    }

    // ----- Relationships ------------------------------------------------

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function dorm(): BelongsTo
    {
        return $this->belongsTo(Dorm::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    // ----- Helpers ------------------------------------------------------

    /** Statuses in which the order is still active (not closed out). */
    public function isOpen(): bool
    {
        return ! in_array($this->status, ['completed', 'cancelled', 'rejected'], true);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /** Zero-based index of the current status within the pipeline, or null if closed abnormally. */
    public function pipelineIndex(): ?int
    {
        $i = array_search($this->status, self::PIPELINE, true);

        return $i === false ? null : $i;
    }
}
