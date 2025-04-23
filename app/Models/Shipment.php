<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'tracking_number',
        'shipping_company',
        'status',
        'notes',
        'tracking_details',
        'shipped_at',
        'delivered_at'
    ];

    protected $casts = [
        'tracking_details' => 'array'
    ];

    protected $dates = [
        'shipped_at',
        'delivered_at'
    ];

    /**
     * Get the order associated with this shipment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}