<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with('campaign')
            ->latest()
            ->paginate(15);
        
        return view('admin.promotions.index', compact('promotions'));
    }
    
    public function create(Request $request)
    {
        $campaigns = Campaign::all();
        $products = Product::where('is_active', true)->get();
        
        // Si se proporciona campaign_id desde una campaña específica
        $selectedCampaignId = $request->campaign_id;
        
        return view('admin.promotions.create', compact('campaigns', 'products', 'selectedCampaignId'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'promotion_type' => 'required|in:featured_product,bundle,buy_x_get_y,discount',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'priority' => 'nullable|integer|min:0',
        ]);
        
        // Preparar reglas según el tipo de promoción
        $rules = [];
        
        switch ($request->promotion_type) {
            case 'buy_x_get_y':
                $request->validate([
                    'buy_product_id' => 'required|exists:products,id',
                    'get_product_id' => 'required|exists:products,id',
                    'buy_quantity' => 'required|integer|min:1',
                    'get_quantity' => 'required|integer|min:1',
                    'get_discount_percentage' => 'required|numeric|min:1|max:100',
                ]);
                
                $rules = [
                    'buy_product_id' => $request->buy_product_id,
                    'get_product_id' => $request->get_product_id,
                    'buy_quantity' => $request->buy_quantity,
                    'get_quantity' => $request->get_quantity,
                    'get_discount_percentage' => $request->get_discount_percentage,
                ];
                break;
                
            case 'bundle':
                $request->validate([
                    'bundle_discount_percentage' => 'required|numeric|min:1|max:100',
                    'bundle_min_quantity' => 'required|integer|min:2',
                ]);
                
                $rules = [
                    'bundle_discount_percentage' => $request->bundle_discount_percentage,
                    'bundle_min_quantity' => $request->bundle_min_quantity,
                ];
                break;
                
            case 'discount':
                $request->validate([
                    'discount_percentage' => 'required|numeric|min:1|max:100',
                ]);
                
                $rules = [
                    'discount_percentage' => $request->discount_percentage,
                ];
                break;
        }
        
        // Crear la promoción
        $promotion = Promotion::create([
            'name' => $request->name,
            'description' => $request->description,
            'campaign_id' => $request->campaign_id,
            'promotion_type' => $request->promotion_type,
            'rules' => $rules,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'is_active' => $request->has('is_active'),
            'priority' => $request->priority ?? 0,
        ]);
        
        // Asignar productos
        $promotion->products()->attach($request->products);
        
        if ($request->campaign_id) {
            return redirect()->route('admin.campaigns.show', $request->campaign_id)
                ->with('success', 'Promoción creada correctamente.');
        }
        
        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promoción creada correctamente.');
    }
    
    public function edit(Promotion $promotion)
    {
        $campaigns = Campaign::all();
        $products = Product::where('is_active', true)->get();
        $selectedProducts = $promotion->products->pluck('id')->toArray();
        
        return view('admin.promotions.edit', compact('promotion', 'campaigns', 'products', 'selectedProducts'));
    }
    
    public function update(Request $request, Promotion $promotion)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'promotion_type' => 'required|in:featured_product,bundle,buy_x_get_y,discount',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'priority' => 'nullable|integer|min:0',
        ]);
        
        // Preparar reglas según el tipo de promoción
        $rules = [];
        
        switch ($request->promotion_type) {
            case 'buy_x_get_y':
                $request->validate([
                    'buy_product_id' => 'required|exists:products,id',
                    'get_product_id' => 'required|exists:products,id',
                    'buy_quantity' => 'required|integer|min:1',
                    'get_quantity' => 'required|integer|min:1',
                    'get_discount_percentage' => 'required|numeric|min:1|max:100',
                ]);
                
                $rules = [
                    'buy_product_id' => $request->buy_product_id,
                    'get_product_id' => $request->get_product_id,
                    'buy_quantity' => $request->buy_quantity,
                    'get_quantity' => $request->get_quantity,
                    'get_discount_percentage' => $request->get_discount_percentage,
                ];
                break;
                
            case 'bundle':
                $request->validate([
                    'bundle_discount_percentage' => 'required|numeric|min:1|max:100',
                    'bundle_min_quantity' => 'required|integer|min:2',
                ]);
                
                $rules = [
                    'bundle_discount_percentage' => $request->bundle_discount_percentage,
                    'bundle_min_quantity' => $request->bundle_min_quantity,
                ];
                break;
                
            case 'discount':
                $request->validate([
                    'discount_percentage' => 'required|numeric|min:1|max:100',
                ]);
                
                $rules = [
                    'discount_percentage' => $request->discount_percentage,
                ];
                break;
        }
        
        // Actualizar la promoción
        $promotion->update([
            'name' => $request->name,
            'description' => $request->description,
            'campaign_id' => $request->campaign_id,
            'promotion_type' => $request->promotion_type,
            'rules' => $rules,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'is_active' => $request->has('is_active'),
            'priority' => $request->priority ?? 0,
        ]);
        
        // Sincronizar productos
        $promotion->products()->sync($request->products);
        
        if ($request->campaign_id) {
            return redirect()->route('admin.campaigns.show', $request->campaign_id)
                ->with('success', 'Promoción actualizada correctamente.');
        }
        
        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promoción actualizada correctamente.');
    }
    
    public function destroy(Promotion $promotion)
    {
        $campaignId = $promotion->campaign_id;
        
        $promotion->products()->detach();
        $promotion->delete();
        
        if ($campaignId && request()->has('from_campaign')) {
            return redirect()->route('admin.campaigns.show', $campaignId)
                ->with('success', 'Promoción eliminada correctamente.');
        }
        
        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promoción eliminada correctamente.');
    }
}