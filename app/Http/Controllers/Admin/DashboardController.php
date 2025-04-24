<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends AdminController
{
    public function index()
    {
        // Obtener estadísticas para el dashboard
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'recent_orders' => Order::with(['user', 'items'])
                                ->latest()
                                ->take(5)
                                ->get(),
            'low_stock_products' => Product::whereHas('inventory', function($query) {
                                        $query->where('quantity', '<', 10);
                                    })
                                    ->take(5)
                                    ->get(),
            'monthly_sales' => $this->getMonthlyStats()
        ];
        
        return view('admin.dashboard.index', compact('stats'));
    }
    
    protected function getMonthlyStats()
    {
        // Obtener estadísticas mensuales para gráficos
        $start = Carbon::now()->subMonths(6)->startOfMonth();
        
        $monthlySales = Order::where('created_at', '>=', $start)
            ->selectRaw('sum(total) as revenue, MONTH(created_at) as month, YEAR(created_at) as year')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                    'revenue' => $item->revenue
                ];
            });
            
        return $monthlySales;
    }
}