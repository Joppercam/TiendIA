<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'base_price',
        'estimated_days',
        'is_active'
    ];

    /**
     * Get all orders using this delivery method.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}