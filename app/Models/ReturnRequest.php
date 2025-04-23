<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'status',
        'admin_notes',
        'approved_at',
        'rejected_at',
        'completed_at'
    ];

    protected $dates = [
        'approved_at',
        'rejected_at',
        'completed_at'
    ];

    /**
     * Get the order associated with this return request.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user that created this return request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items included in this return request.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ReturnRequestItem::class);
    }
}