<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends AdminController
{
    protected $reportService;
    
    public function __construct(ReportService $reportService)
    {
        parent::__construct();
        $this->reportService = $reportService;
    }
    
    public function index()
    {
        return view('admin.reports.index');
    }
    
    public function sales(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'group_by' => 'nullable|in:day,week,month'
        ]);
        
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $groupBy = $request->group_by ?? 'day';
        
        $report = $this->reportService->generateSalesReport($startDate, $endDate, $groupBy);
        
        return view('admin.reports.sales', compact('report', 'startDate', 'endDate', 'groupBy'));
    }
    
    public function products(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:5|max:100'
        ]);
        
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $limit = $request->limit ?? 20;
        
        $report = $this->reportService->generateProductsReport($startDate, $endDate, $limit);
        
        return view('admin.reports.products', compact('report', 'startDate', 'endDate', 'limit'));
    }
    
    public function customers(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:5|max:100'
        ]);
        
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $limit = $request->limit ?? 20;
        
        $report = $this->reportService->generateCustomersReport($startDate, $endDate, $limit);
        
        return view('admin.reports.customers', compact('report', 'startDate', 'endDate', 'limit'));
    }
    
    public function export(Request $request, $type)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,xlsx,pdf'
        ]);
        
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $format = $request->format;
        
        return $this->reportService->exportReport($type, $startDate, $endDate, $format);
    }
}