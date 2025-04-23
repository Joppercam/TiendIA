<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'min_stock',
        'shelf_location',
        'warehouse_location',
        'notes',
    ];

    /**
     * Relación con el producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Obtiene los movimientos asociados a este inventario
     */
    public function movements()
    {
        return InventoryMovement::where('product_id', $this->product_id)->orderBy('created_at', 'desc');
    }

    /**
     * Verifica si el producto tiene stock bajo
     */
    public function hasLowStock(): bool
    {
        return $this->quantity <= $this->min_stock;
    }
}