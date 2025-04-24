// app/Services/AnalyticsService.php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getProductPerformance($productId, $period = 30)
    {
        $startDate = Carbon::now()->subDays($period);
        
        $sales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $productId)
            ->where('orders.created_at', '>=', $startDate)
            ->selectRaw('DATE(orders.created_at) as date, SUM(order_items.quantity) as quantity, SUM(order_items.price * order_items.quantity) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $product = Product::with(['category', 'brand'])->find($productId);
        
        $views = $this->getProductViews($productId, $period);
        
        $conversionRate = $this->calculateProductConversionRate($views->sum('views'), $sales->sum('quantity'));
        
        return [
            'product' => $product,
            'sales_data' => $sales,
            'views_data' => $views,
            'total_sales' => $sales->sum('quantity'),
            'total_revenue' => $sales->sum('revenue'),
            'total_views' => $views->sum('views'),
            'conversion_rate' => $conversionRate,
            'period' => $period
        ];
    }
    
    public function getCategoryAnalytics($categoryId = null, $period = 30)
    {
        $startDate = Carbon::now()->subDays($period);
        
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.created_at', '>=', $startDate);
            
        if ($categoryId) {
            $query->where('categories.id', $categoryId);
        }
        
        $categoryData = $query->select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw('SUM(order_items.quantity) as products_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('revenue', 'desc')
            ->get();
            
        $topProducts = null;
        
        if ($categoryId) {
            $topProducts = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.category_id', $categoryId)
                ->where('orders.created_at', '>=', $startDate)
                ->select(
                    'products.id',
                    'products.name',
                    DB::raw('SUM(order_items.quantity) as quantity'),
                    DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
                )
                ->groupBy('products.id', 'products.name')
                ->orderBy('quantity', 'desc')
                ->limit(10)
                ->get();
                
            $category = Category::find($categoryId);
        } else {
            $category = null;
        }
        
        return [
            'category' => $category,
            'category_data' => $categoryData,
            'top_products' => $topProducts,
            'period' => $period
        ];
    }
    
    public function getCustomerAnalytics($userId = null, $period = 30)
    {
        $startDate = Carbon::now()->subDays($period);
        
        if ($userId) {
            $user = User::find($userId);
            
            $orderStats = Order::where('user_id', $userId)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('COUNT(*) as orders_count, SUM(total) as total_spent, AVG(total) as avg_order_value')
                ->first();
                
            $recentOrders = Order::where('user_id', $userId)
                ->with(['items.product'])
                ->latest()
                ->limit(5)
                ->get();
                
            $favoriteCategories = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('orders.user_id', $userId)
                ->where('orders.created_at', '>=', $startDate)
                ->select(
                    'categories.id',
                    'categories.name',
                    DB::raw('SUM(order_items.quantity) as quantity'),
                    DB::raw('SUM(order_items.price * order_items.quantity) as spent')
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('quantity', 'desc')
                ->limit(5)
                ->get();
                
            return [
                'user' => $user,
                'order_stats' => $orderStats,
                'recent_orders' => $recentOrders,
                'favorite_categories' => $favoriteCategories,
                'period' => $period
            ];
        } else {
            // Análisis general de clientes
            $topCustomers = DB::table('orders')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->where('orders.created_at', '>=', $startDate)
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    DB::raw('COUNT(*) as orders_count'),
                    DB::raw('SUM(orders.total) as total_spent'),
                    // Continuación de app/Services/AnalyticsService.php
                    DB::raw('AVG(orders.total) as avg_order_value')
                )
                ->groupBy('users.id', 'users.name', 'users.email')
                ->orderBy('total_spent', 'desc')
                ->limit(10)
                ->get();
                
            $newVsReturning = [
                'new' => Order::join('users', 'orders.user_id', '=', 'users.id')
                    ->where('orders.created_at', '>=', $startDate)
                    ->whereRaw('orders.id = (SELECT MIN(id) FROM orders WHERE user_id = users.id)')
                    ->count(),
                'returning' => DB::table('orders')
                    ->join('users', 'orders.user_id', '=', 'users.id')
                    ->where('orders.created_at', '>=', $startDate)
                    ->whereRaw('orders.id != (SELECT MIN(id) FROM orders WHERE user_id = users.id)')
                    ->distinct('users.id')
                    ->count('users.id')
            ];
            
            return [
                'top_customers' => $topCustomers,
                'new_vs_returning' => $newVsReturning,
                'period' => $period
            ];
        }
    }
    
    protected function getProductViews($productId, $period = 30)
    {
        // Esta función simula datos de vistas de productos
        // En una implementación real, esto se obtendría de una tabla de analytics
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();
        
        $views = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $views[] = [
                'date' => $currentDate->format('Y-m-d'),
                'views' => rand(10, 200) // Valor simulado
            ];
            $currentDate->addDay();
        }
        
        return collect($views);
    }
    
    protected function calculateProductConversionRate($views, $purchases)
    {
        if ($views == 0) return 0;
        return round(($purchases / $views) * 100, 2);
    }
}