<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\PromotionService;
use App\Services\CartService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected $promotionService;
    protected $cartService;
    
    public function __construct(PromotionService $promotionService, CartService $cartService)
    {
        $this->promotionService = $promotionService;
        $this->cartService = $cartService;
    }
    
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);
        
        $cart = $this->cartService->getCart();
        
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'El carrito está vacío.',
            ]);
        }
        
        $result = $this->promotionService->applyCouponToCart($cart, $request->code);
        
        return response()->json([
            'success' => $result['valid'],
            'message' => $result['message'],
            'data' => $result['valid'] ? [
                'discount' => $result['discount'],
                'formatted_discount' => $result['formatted_discount'],
            ] : null,
        ]);
    }
    
    public function remove()
    {
        $cart = $this->cartService->getCart();
        
        if ($cart) {
            $cart->coupon_code = null;
            $cart->coupon_discount = 0;
            $cart->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Cupón removido correctamente.',
        ]);
    }
}