<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'service',
        'unit_price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /** Human-readable label for the service type. */
    public function serviceLabel(): string
    {
        return match ($this->service) {
            'wash' => 'Wash',
            'iron' => 'Iron',
            'wash_iron' => 'Wash & Iron',
            'dry_clean' => 'Dry Clean',
            default => ucfirst((string) $this->service),
        };
    }
}
