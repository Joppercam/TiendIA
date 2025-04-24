<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::latest()->paginate(15);
        
        return view('admin.discounts.index', compact('discounts'));
    }
    
    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        
        return view('admin.discounts.create', compact('products', 'categories', 'brands'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'scope' => 'required|in:product,category,brand,cart',
            'scope_id' => 'required_unless:scope,cart|nullable|integer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'description' => 'nullable|string|max:500',
        ]);
        
        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'El porcentaje no puede ser mayor a 100%'])->withInput();
        }
        
        $discount = Discount::create([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'scope' => $request->scope,
            'scope_id' => $request->scope !== 'cart' ? $request->scope_id : null,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
        ]);
        
        return redirect()->route('admin.discounts.index')
            ->with('success', 'Descuento creado correctamente.');
    }
    
    public function edit(Discount $discount)
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        
        return view('admin.discounts.edit', compact('discount', 'products', 'categories', 'brands'));
    }
    
    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'scope' => 'required|in:product,category,brand,cart',
            'scope_id' => 'required_unless:scope,cart|nullable|integer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'description' => 'nullable|string|max:500',
        ]);
        
        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'El porcentaje no puede ser mayor a 100%'])->withInput();
        }
        
        $discount->update([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'scope' => $request->scope,
            'scope_id' => $request->scope !== 'cart' ? $request->scope_id : null,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
        ]);
        
        return redirect()->route('admin.discounts.index')
            ->with('success', 'Descuento actualizado correctamente.');
    }
    
    public function destroy(Discount $discount)
    {
        $discount->delete();
        
        return redirect()->route('admin.discounts.index')
            ->with('success', 'Descuento eliminado correctamente.');
    }
}