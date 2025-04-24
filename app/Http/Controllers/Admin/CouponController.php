<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(15);
        
        return view('admin.coupons.index', compact('coupons'));
    }
    
    public function create()
    {
        return view('admin.coupons.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'description' => 'nullable|string|max:500',
        ]);
        
        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'El porcentaje no puede ser mayor a 100%'])->withInput();
            // app/Http/Controllers/Admin/CouponController.php (continuación)
        }
        
        $coupon = Coupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_purchase' => $request->min_purchase,
            'usage_limit' => $request->usage_limit,
            'usage_count' => 0,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
        ]);
        
        return redirect()->route('admin.coupons.index')
            ->with('success', 'Cupón creado correctamente.');
    }
    
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }
    
    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code,'.$coupon->id,
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'description' => 'nullable|string|max:500',
        ]);
        
        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'El porcentaje no puede ser mayor a 100%'])->withInput();
        }
        
        $coupon->update([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_purchase' => $request->min_purchase,
            'usage_limit' => $request->usage_limit,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
        ]);
        
        return redirect()->route('admin.coupons.index')
            ->with('success', 'Cupón actualizado correctamente.');
    }
    
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        
        return redirect()->route('admin.coupons.index')
            ->with('success', 'Cupón eliminado correctamente.');
    }
    
    public function generateCode()
    {
        // Generar un código aleatorio
        $code = strtoupper(Str::random(8));
        
        // Verificar que no exista ya
        while (Coupon::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(8));
        }
        
        return response()->json(['code' => $code]);
    }
}