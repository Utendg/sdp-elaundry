<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminPermission extends Model
{
    public const WORKERS = 'workers';
    public const PRICES = 'prices';
    public const DORMS = 'dorms';
    public const ORDERS = 'orders';
    public const COMPLAINTS = 'complaints';

    /** Held only by the Head of Residence Life: create admins and assign responsibilities. */
    public const ADMINS = 'admins';

    /** Responsibilities the Head of Residence Life can give to other admins. */
    public const GRANTABLE = [
        self::WORKERS => 'Laundry workers (approve, decline, revoke)',
        self::ORDERS => 'Order monitoring',
        self::COMPLAINTS => 'Complaints',
        self::PRICES => 'Price list',
        self::DORMS => 'Dorms',
    ];

    protected $fillable = ['user_id', 'permission', 'granted_by'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
