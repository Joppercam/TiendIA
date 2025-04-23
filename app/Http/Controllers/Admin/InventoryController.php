<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
        $this->middleware('permission:view-inventory')->only(['index', 'show']);
        $this->middleware('permission:edit-inventory')->only(['edit', 'update']);
    }

    /**
     * Mostrar listado de inventario
     */
    public function index(Request $request)
    {
        $query = Inventory::with('product');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('low_stock')) {
            $query->whereRaw('quantity <= min_stock');
        }

        $inventories = $query->paginate(15);
        
        return view('admin.inventory.index', compact('inventories'));
    }

    /**
     * Mostrar detalles de un producto en inventario
     */
    public function show(Inventory $inventory)
    {
        $inventory->load('product');
        $movements = $inventory->movements()->paginate(10);
        
        return view('admin.inventory.show', compact('inventory', 'movements'));
    }

    /**
     * Mostrar formulario para editar
     */
    public function edit(Inventory $inventory)
    {
        $inventory->load('product');
        return view('admin.inventory.edit', compact('inventory'));
    }

    /**
     * Actualizar el inventario
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'min_stock' => 'required|integer|min:0',
            'shelf_location' => 'nullable|string|max:255',
            'warehouse_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            // Si se cambió la cantidad, registrar un movimiento
            if ($request->filled('quantity') && $request->input('quantity') != $inventory->quantity) {
                $this->inventoryService->adjustStock(
                    $inventory->product_id, 
                    $request->input('quantity') - $inventory->quantity, 
                    'adjustment', 
                    auth()->id(), 
                    null, 
                    null, 
                    'Ajuste manual desde panel administrativo',
                    $request->input('notes')
                );
            }

            // Actualizar campos regulares
            $inventory->update([
                'min_stock' => $request->input('min_stock'),
                'shelf_location' => $request->input('shelf_location'),
                'warehouse_location' => $request->input('warehouse_location'),
                'notes' => $request->input('notes'),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.inventory.show', $inventory)
                ->with('success', 'Inventario actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el inventario: ' . $e->getMessage());
        }
    }
}