<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
        'order',
        'is_default'
    ];

    /**
     * Get all orders with this status.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}