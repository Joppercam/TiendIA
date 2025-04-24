<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function generateSalesReport($startDate, $endDate, $groupBy = 'day')
    {
        $formatString = $this->getFormatStringForGrouping($groupBy);
        
        $results = Order::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->selectRaw("DATE_FORMAT(created_at, '{$formatString}') as date")
            ->selectRaw('SUM(total) as revenue')
            ->selectRaw('SUM(subtotal) as subtotal')
            ->selectRaw('SUM(tax) as tax')
            ->selectRaw('SUM(shipping_cost) as shipping')
            ->selectRaw('SUM(discount) as discount')
            ->selectRaw('COUNT(*) as orders_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $report = [
            'data' => $results,
            'totals' => [
                'revenue' => $results->sum('revenue'),
                'subtotal' => $results->sum('subtotal'),
                'tax' => $results->sum('tax'),
                'shipping' => $results->sum('shipping'),
                'discount' => $results->sum('discount'),
                'orders_count' => $results->sum('orders_count')
            ]
        ];
        
        return $report;
    }
    
    public function generateProductsReport($startDate, $endDate, $limit = 20)
    {
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.created_at', '>=', $startDate)
            ->where('orders.created_at', '<=', $endDate)
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('quantity_sold', 'desc')
            ->limit($limit)
            ->get();
            
        $lowStock = Product::whereHas('inventory', function($query) {
                    $query->where('quantity', '<', 10);
                })
                ->with('inventory')
                ->limit($limit)
                ->get();
                
        $report = [
            'top_products' => $topProducts,
            'low_stock' => $lowStock
        ];
        
        return $report;
    }
    
    public function generateCustomersReport($startDate, $endDate, $limit = 20)
    {
        $topCustomers = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.created_at', '>=', $startDate)
            ->where('orders.created_at', '<=', $endDate)
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('SUM(orders.total) as total_spent'),
                DB::raw('AVG(orders.total) as average_order_value')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get();
            
        $newCustomers = User::where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', $endDate)
                        ->count();
                        
        $report = [
            'top_customers' => $topCustomers,
            'new_customers' => $newCustomers,
            'total_customers' => User::count()
        ];
        
        return $report;
    }
    
    public function exportReport($type, $startDate, $endDate, $format)
    {
        // Implementación de exportación con Laravel Excel
        // Esta implementación dependerá de la librería que se use para exportación
        
        switch ($type) {
            case 'sales':
                $report = $this->generateSalesReport($startDate, $endDate, 'day');
                $filename = "sales_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
                break;
            case 'products':
                $report = $this->generateProductsReport($startDate, $endDate, 100);
                $filename = "products_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
                break;
            case 'customers':
                $report = $this->generateCustomersReport($startDate, $endDate, 100);
                $filename = "customers_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
                break;
            default:
                return redirect()->back()->with('error', 'Tipo de reporte no válido');
        }
        
        // Aquí iría la lógica para exportar el reporte en el formato elegido
        // Este es un ejemplo básico que devolvería un array
        return [
            'data' => $report,
            'filename' => $filename,
            'format' => $format
        ];
    }
    
    protected function getFormatStringForGrouping($groupBy)
    {
        switch ($groupBy) {
            case 'day':
                return '%Y-%m-%d';
            case 'week':
                return '%x-W%v';
            case 'month':
                return '%Y-%m';
            default:
                return '%Y-%m-%d';
        }
    }
}