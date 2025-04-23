<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Obtener el carrito asociado.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Obtener el producto asociado.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Obtener el subtotal del ítem.
     */
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}