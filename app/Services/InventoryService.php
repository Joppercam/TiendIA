<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\LowStockAlert;

class InventoryService
{
    /**
     * Inicializar inventario para un producto
     */
    public function initializeInventory(Product $product, int $initialQuantity = 0): Inventory
    {
        return Inventory::create([
            'product_id' => $product->id,
            'quantity' => $initialQuantity,
            'min_stock' => 5, // Valor por defecto
        ]);
    }

    /**
     * Ajustar el stock de un producto
     */
    public function adjustStock(
        int $productId, 
        int $quantity, 
        string $type, 
        ?int $userId = null, 
        ?int $supplierId = null, 
        ?int $orderId = null, 
        ?string $reference = null,
        ?string $notes = null
    ): InventoryMovement {
        return DB::transaction(function () use ($productId, $quantity, $type, $userId, $supplierId, $orderId, $reference, $notes) {
            // Obtener o crear el inventario
            $inventory = Inventory::firstOrCreate(
                ['product_id' => $productId],
                ['quantity' => 0, 'min_stock' => 5]
            );
            
            $previousQuantity = $inventory->quantity;
            $newQuantity = $previousQuantity + $quantity;
            
            // Actualizar la cantidad
            $inventory->quantity = $newQuantity;
            $inventory->save();
            
            // Registrar el movimiento
            $movement = InventoryMovement::create([
                'product_id' => $productId,
                'user_id' => $userId,
                'supplier_id' => $supplierId,
                'order_id' => $orderId,
                'type' => $type,
                'quantity' => $quantity,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'reference' => $reference,
                'notes' => $notes,
            ]);
            
            // Si el stock está por debajo del mínimo, registrarlo
            if ($newQuantity <= $inventory->min_stock) {
                $this->registerLowStockAlert($inventory);
            }
            
            return $movement;
        });
    }
    
    /**
     * Registrar una alerta de stock bajo
     */
    private function registerLowStockAlert(Inventory $inventory): void
    {
        // Registrar en log (en producción podría enviar notificaciones)
        Log::channel('inventory')->warning("Stock bajo para producto ID: {$inventory->product_id}. Cantidad actual: {$inventory->quantity}, Mínimo requerido: {$inventory->min_stock}");
        
       // Disparar evento para notificaciones
       event(new LowStockAlert($inventory));
    }
    
    /**
     * Obtener productos con stock bajo
     */
    public function getLowStockProducts()
    {
        return Inventory::whereRaw('quantity <= min_stock')
            ->with('product')
            ->get();
    }
    
    /**
     * Verificar si un producto tiene suficiente stock
     */
    public function hasEnoughStock(int $productId, int $requiredQuantity): bool
    {
        $inventory = Inventory::where('product_id', $productId)->first();
        
        if (!$inventory) {
            return false;
        }
        
        return $inventory->quantity >= $requiredQuantity;
    }

    
}