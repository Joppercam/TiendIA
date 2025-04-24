<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function getDashboardStats()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $previousMonth = $now->copy()->subMonth();
        $startOfPreviousMonth = $previousMonth->copy()->startOfMonth();
        $endOfPreviousMonth = $previousMonth->copy()->endOfMonth();
        
        $stats = [
            'current_month' => [
                'sales' => $this->getMonthSales($startOfMonth, $endOfMonth),
                'orders' => $this->getMonthOrders($startOfMonth, $endOfMonth),
                'average_order' => $this->getAverageOrderValue($startOfMonth, $endOfMonth),
                'new_customers' => $this->getNewCustomers($startOfMonth, $endOfMonth)
            ],
            'previous_month' => [
                'sales' => $this->getMonthSales($startOfPreviousMonth, $endOfPreviousMonth),
                'orders' => $this->getMonthOrders($startOfPreviousMonth, $endOfPreviousMonth),
                'average_order' => $this->getAverageOrderValue($startOfPreviousMonth, $endOfPreviousMonth),
                'new_customers' => $this->getNewCustomers($startOfPreviousMonth, $endOfPreviousMonth)
            ],
            'today' => [
                'sales' => $this->getTodaySales(),
                'orders' => $this->getTodayOrders(),
                'visitors' => $this->getTodayVisitors(),
                'conversion_rate' => $this->getConversionRate()
            ],
            'totals' => [
                'products' => Product::count(),
                'customers' => User::whereHas('roles', function($q) {
                    $q->where('name', 'customer');
                })->count(),
                'orders' => Order::count(),
                'revenue' => Order::sum('total')
            ]
        ];
        
        // Calcular porcentajes de cambio
        $stats['changes'] = [
            'sales' => $this->calculatePercentageChange(
                $stats['current_month']['sales'],
                $stats['previous_month']['sales']
            ),
            'orders' => $this->calculatePercentageChange(
                $stats['current_month']['orders'],
                $stats['previous_month']['orders']
            ),
            'average_order' => $this->calculatePercentageChange(
                $stats['current_month']['average_order'],
                $stats['previous_month']['average_order']
            ),
            'new_customers' => $this->calculatePercentageChange(
                $stats['current_month']['new_customers'],
                $stats['previous_month']['new_customers']
            )
        ];
        
        return $stats;
    }
    
    protected function getMonthSales($start, $end)
    {
        return Order::whereBetween('created_at', [$start, $end])->sum('total');
    }
    
    protected function getMonthOrders($start, $end)
    {
        return Order::whereBetween('created_at', [$start, $end])->count();
    }
    
    protected function getAverageOrderValue($start, $end)
    {
        $result = Order::whereBetween('created_at', [$start, $end])->avg('total');
        return $result ?: 0;
    }
    
    protected function getNewCustomers($start, $end)
    {
        return User::whereBetween('created_at', [$start, $end])
                ->whereHas('roles', function($q) {
                    $q->where('name', 'customer');
                })
                ->count();
    }
    
    protected function getTodaySales()
    {
        return Order::whereDate('created_at', Carbon::today())->sum('total');
    }
    
    protected function getTodayOrders()
    {
        return Order::whereDate('created_at', Carbon::today())->count();
    }
    
    protected function getTodayVisitors()
    {
        // Esta implementación dependerá de cómo se rastreen las visitas
        // Aquí usamos un valor dummy
        return rand(100, 500);
    }
    
    protected function getConversionRate()
    {
        $visitors = $this->getTodayVisitors();
        if ($visitors == 0) return 0;
        
        $orders = $this->getTodayOrders();
        return ($orders / $visitors) * 100;
    }
    
    protected function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        
        return (($current - $previous) / $previous) * 100;
    }
    
    // Métodos para obtener datos para gráficos
    
    public function getSalesChartData($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return Order::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total' => $item->total
                ];
            });
    }
    
    public function getTopSellingProducts($limit = 5)
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('quantity', 'desc')
            ->limit($limit)
            ->get();
    }
}