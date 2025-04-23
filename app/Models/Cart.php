<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'session_id',
        'is_guest',
    ];

    protected $casts = [
        'is_guest' => 'boolean',
    ];

    /**
     * Obtener los elementos del carrito.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Obtener el usuario asociado al carrito.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el subtotal del carrito.
     */
    public function getSubtotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Obtener el total de elementos en el carrito.
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }
}