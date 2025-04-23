<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryDashboardController extends Controller
{
    protected $inventoryService;
    
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
        $this->middleware('permission:view-inventory');
    }
    
    public function index()
    {
        // Productos con stock bajo
        $lowStockProducts = $this->inventoryService->getLowStockProducts();
        
        // Total de productos en inventario
        $totalProducts = Inventory::count();
        
        // Valor total del inventario
        $inventoryValue = DB::table('inventories')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->select(DB::raw('SUM(inventories.quantity * products.price) as total_value'))
            ->first()
            ->total_value ?? 0;
        
        // Movimientos recientes
        $recentMovements = InventoryMovement::with(['product', 'supplier'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Productos sin stock
        $outOfStockProducts = Inventory::where('quantity', 0)
            ->with('product')
            ->get();
        
        // Productos más vendidos (últimos 30 días)
        $topSellingProducts = InventoryMovement::where('type', 'sale')
            ->where('created_at', '>=', now()->subDays(30))
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->with('product')
            ->get();
        
        return view('admin.inventory.dashboard', compact(
            'lowStockProducts',
            'totalProducts',
            'inventoryValue',
            'recentMovements',
            'outOfStockProducts',
            'topSellingProducts'
        ));
    }
}