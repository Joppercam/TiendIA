<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'order_status_id',
        'payment_method_id',
        'delivery_method_id',
        'address_id',
        'subtotal',
        'tax',
        'shipping_cost',
        'discount',
        'total',
        'currency',
        'payment_status',
        'shipping_status',
        'notes',
        'guest_email',
        'guest_name',
        'is_guest_checkout',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at'
    ];

    protected $dates = [
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'payment_status',
        'payment_method',
    ];

    protected $casts = [
        // Casts existentes...
        'paid_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status of the order.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    /**
     * Get the payment method used for this order.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the delivery method used for this order.
     */
    public function deliveryMethod(): BelongsTo
    {
        return $this->belongsTo(DeliveryMethod::class);
    }

    /**
     * Get the address associated with this order.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get all items in this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the shipment associated with this order.
     */
    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    // Relación con factura
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    
    // Método para verificar si el pedido está pagado
    public function isPaid()
    {
        return $this->payment_status === 'completed';
    }
}