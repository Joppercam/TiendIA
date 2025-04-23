<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarnos que hay productos
        $products = Product::all();
        
        if ($products->isEmpty()) {
            $this->command->info('No hay productos para generar inventario. Ejecuta primero ProductSeeder.');
            return;
        }
        
        // Obtener los proveedores
        $suppliers = Supplier::all();
        
        if ($suppliers->isEmpty()) {
            $this->command->info('No hay proveedores. Ejecuta primero SupplierSeeder.');
            return;
        }
        
        // Crear inventario para cada producto
        foreach ($products as $product) {
            // Cantidad aleatoria entre 5 y 100
            $quantity = rand(5, 100);
            
            // Crear el registro de inventario
            $inventory = Inventory::create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'min_stock' => rand(5, 20),
                'shelf_location' => 'S' . rand(1, 10) . '-' . chr(rand(65, 90)) . rand(1, 10),
                'warehouse_location' => 'W' . rand(1, 5) . '-' . rand(100, 999),
            ]);
            
            // Crear un movimiento inicial
            InventoryMovement::create([
                'product_id' => $product->id,
                'supplier_id' => $suppliers->random()->id,
                'type' => 'purchase',
                'quantity' => $quantity,
                'previous_quantity' => 0,
                'new_quantity' => $quantity,
                'unit_cost' => $product->price * 0.7, // 70% del precio (margen 30%)
                'reference' => 'Inventario inicial',
            ]);
            
            // Crear algunos movimientos adicionales (50% de probabilidad)
            if (rand(0, 1)) {
                $additionalQuantity = rand(10, 50);
                
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'supplier_id' => $suppliers->random()->id,
                    'type' => 'purchase',
                    'quantity' => $additionalQuantity,
                    'previous_quantity' => $quantity,
                    'new_quantity' => $quantity + $additionalQuantity,
                    'unit_cost' => $product->price * 0.7, // 70% del precio (margen 30%)
                    'reference' => 'Compra adicional',
                ]);
                
                // Actualizar la cantidad en inventario
                $inventory->update([
                    'quantity' => $quantity + $additionalQuantity
                ]);
            }
        }
    }
}