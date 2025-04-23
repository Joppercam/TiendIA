<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'supplier_id',
        'order_id',
        'type',
        'quantity',
        'previous_quantity',
        'new_quantity',
        'unit_cost',
        'reference',
        'notes',
    ];

    /**
     * Los tipos de movimientos disponibles
     */
    const TYPES = [
        'purchase' => 'Compra',
        'sale' => 'Venta',
        'return' => 'Devolución',
        'adjustment' => 'Ajuste',
        'transfer' => 'Transferencia',
    ];

    /**
     * Relación con el producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el proveedor
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relación con la orden
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}