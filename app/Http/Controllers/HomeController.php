<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener productos destacados
        $featuredProducts = Product::where('featured', true)
            ->where('status', 'active')
            ->with(['category', 'brand', 'images' => function($q) {
                $q->where('is_primary', true);
            }])
            ->take(8)
            ->get();
        
        // Obtener categorías principales
        $categories = Category::where('parent_id', null)
            ->where('is_active', true)
            ->take(6)
            ->get();
        
        // Obtener productos nuevos
        $newProducts = Product::where('status', 'active')
            ->with(['category', 'brand', 'images' => function($q) {
                $q->where('is_primary', true);
            }])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();
        
        // Obtener productos en oferta
        $saleProducts = Product::where('status', 'active')
            ->whereNotNull('special_price')
            ->where('special_price_from', '<=', now())
            ->where(function($q) {
                $q->whereNull('special_price_to')
                  ->orWhere('special_price_to', '>=', now());
            })
            ->with(['category', 'brand', 'images' => function($q) {
                $q->where('is_primary', true);
            }])
            ->take(8)
            ->get();
        
        return view('home', compact('featuredProducts', 'categories', 'newProducts', 'saleProducts'));
    }
}
