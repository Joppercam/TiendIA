<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Discount;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Collection;

class PromotionService
{
    /**
     * Validar un cupón y obtener el descuento correspondiente
     */
    public function validateCoupon(string $code, float $cartTotal = 0): array
    {
        $coupon = Coupon::where('code', $code)->first();
        
        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'El cupón no existe.',
            ];
        }
        
        if (!$coupon->isValid()) {
            return [
                'valid' => false,
                'message' => 'El cupón no es válido o ha expirado.',
            ];
        }
        
        if ($coupon->min_purchase && $cartTotal < $coupon->min_purchase) {
            return [
                'valid' => false,
                'message' => "El monto mínimo de compra es de $" . number_format($coupon->min_purchase, 2),
            ];
        }
        
        $discount = $coupon->type === 'percentage' 
            ? ($cartTotal * $coupon->value / 100) 
            : $coupon->value;
            
        return [
            'valid' => true,
            'message' => 'Cupón aplicado correctamente.',
            'coupon' => $coupon,
            'discount' => $discount,
            'formatted_discount' => $coupon->type === 'percentage' 
                ? $coupon->value . '%' 
                : '$' . number_format($coupon->value, 2),
        ];
    }
    
    /**
     * Aplicar un cupón a un carrito
     */
    public function applyCouponToCart(Cart $cart, string $code): array
    {
        $cartTotal = $cart->items->sum(function($item) {
            return $item->quantity * $item->price;
        });
        
        $result = $this->validateCoupon($code, $cartTotal);
        
        if ($result['valid']) {
            $cart->coupon_code = $code;
            $cart->coupon_discount = $result['discount'];
            $cart->save();
        }
        
        return $result;
    }
    
    /**
     * Obtener descuentos aplicables a un producto
     */
    public function getProductDiscounts(Product $product): Collection
    {
        $now = now();
        
        // Obtener descuentos directos del producto
        $productDiscounts = Discount::where('scope', 'product')
            ->where('scope_id', $product->id)
            ->where('is_active', true)
            ->where(function($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->get();
            
        // Obtener descuentos por categoría
        $categoryDiscounts = Discount::where('scope', 'category')
            ->where('scope_id', $product->category_id)
            ->where('is_active', true)
            ->where(function($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->get();
            
        // Obtener descuentos por marca
        $brandDiscounts = Discount::where('scope', 'brand')
            ->where('scope_id', $product->brand_id)
            ->where('is_active', true)
            ->where(function($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->get();
            
        // Obtener promociones activas para el producto
        $promotions = Promotion::getActivePromotions()
            ->filter(function($promotion) use ($product) {
                // Verificar si la promoción aplica al producto
                return $promotion->products->contains($product->id);
            });
            
        // Combinar todos los descuentos
        return $productDiscounts->merge($categoryDiscounts)
            ->merge($brandDiscounts)
            ->sortByDesc('value'); // Devolvemos el de mayor valor
    }
    
    /**
     * Calcular el precio con descuento para un producto
     */
    public function calculateDiscountedPrice(Product $product): array
    {
        $discounts = $this->getProductDiscounts($product);
        
        if ($discounts->isEmpty()) {
            return [
                'has_discount' => false,
                'original_price' => $product->price,
                'final_price' => $product->price,
                'discount_amount' => 0,
                'discount_percentage' => 0,
            ];
        }
        
        // Tomamos el descuento de mayor valor
        $bestDiscount = $discounts->first();
        
        $discountAmount = $bestDiscount->type === 'percentage' 
            ? ($product->price * $bestDiscount->value / 100) 
            : $bestDiscount->value;
            
        $finalPrice = max(0, $product->price - $discountAmount);
        
        $discountPercentage = $bestDiscount->type === 'percentage' 
            ? $bestDiscount->value 
            : round(($discountAmount / $product->price) * 100);
            
        return [
            'has_discount' => true,
            'original_price' => $product->price,
            'final_price' => $finalPrice,
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage,
            'discount_name' => $bestDiscount->name,
        ];
    }
    
    /**
     * Obtener productos destacados por promociones
     */
    public function getFeaturedProducts(int $limit = 8): Collection
    {
        $now = now();
        
        $featuredPromotions = Promotion::where('promotion_type', 'featured_product')
            ->where('is_active', true)
            ->where(function($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('priority', 'desc')
            ->get();
            
        $productIds = [];
        
        foreach ($featuredPromotions as $promotion) {
            $productIds = array_merge($productIds, $promotion->products->pluck('id')->toArray());
            
            if (count($productIds) >= $limit) {
                break;
            }
        }
        
        $productIds = array_slice(array_unique($productIds), 0, $limit);
        
        if (empty($productIds)) {
            // Si no hay promociones, devolver productos populares
            return Product::where('is_active', true)
                ->orderBy('views', 'desc')
                ->take($limit)
                ->get();
        }
        
        return Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->get();
    }
    
    /**
     * Aplicar promociones de carrito
     */
    public function applyCartPromotions(Cart $cart): array
    {
        $appliedPromotions = [];
        $cartItems = $cart->items;
        
        // Obtenemos las promociones activas
        $activePromotions = Promotion::getActivePromotions();
        
        // Procesamos promociones de tipo "buy_x_get_y"
        $buyXGetYPromotions = $activePromotions->where('promotion_type', 'buy_x_get_y');
        
        foreach ($buyXGetYPromotions as $promotion) {
            $rules = $promotion->rules;
            
            // Verificar si se cumplen las condiciones
            if (isset($rules['buy_product_id']) && isset($rules['get_product_id']) && 
                isset($rules['buy_quantity']) && isset($rules['get_quantity']) && 
                isset($rules['get_discount_percentage'])) {
                
                $buyProductId = $rules['buy_product_id'];
                $getProductId = $rules['get_product_id'];
                $buyQuantity = $rules['buy_quantity'];
                $getQuantity = $rules['get_quantity'];
                $getDiscountPercentage = $rules['get_discount_percentage'];
                
                // Buscar los productos en el carrito
                $buyItem = $cartItems->firstWhere('product_id', $buyProductId);
                $getItem = $cartItems->firstWhere('product_id', $getProductId);
                
                if ($buyItem && $getItem && $buyItem->quantity >= $buyQuantity) {
                    // Calcular cuántas veces se puede aplicar la promoción
                    $timesToApply = floor($buyItem->quantity / $buyQuantity);
                    $itemsToDiscount = min($timesToApply * $getQuantity, $getItem->quantity);
                    
                    if ($itemsToDiscount > 0) {
                        $getProduct = Product::find($getProductId);
                        $discountAmount = ($getProduct->price * $getDiscountPercentage / 100) * $itemsToDiscount;
                        
                        $appliedPromotions[] = [
                            'promotion_id' => $promotion->id,
                            'promotion_name' => $promotion->name,
                            'discount_amount' => $discountAmount,
                            'description' => "Compra {$buyQuantity} del producto #{$buyProductId} y recibe {$getDiscountPercentage}% de descuento en {$itemsToDiscount} unidades del producto #{$getProductId}",
                        ];
                    }
                }
            }
        }
        
        // Procesar otras promociones aquí...
        
        // Calcular descuento total
        $totalDiscount = collect($appliedPromotions)->sum('discount_amount');
        
        return [
            'promotions' => $appliedPromotions,
            'total_discount' => $totalDiscount,
        ];
    }
}