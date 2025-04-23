<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    protected $cartService;
    protected $paymentService;
    protected $inventoryService;
    
    protected $shippingAddress;
    protected $billingAddress;
    protected $paymentMethod;
    protected $checkoutSession;

    public function __construct(
        CartService $cartService, 
        PaymentService $paymentService,
        InventoryService $inventoryService
    ) {
        $this->cartService = $cartService;
        $this->paymentService = $paymentService;
        $this->inventoryService = $inventoryService;
        
        // Restore checkout session data if available
        if (session()->has('checkout')) {
            $this->checkoutSession = session('checkout');
            
            if (isset($this->checkoutSession['shipping_address_id'])) {
                $this->shippingAddress = Address::find($this->checkoutSession['shipping_address_id']);
            }
            
            if (isset($this->checkoutSession['billing_address_id'])) {
                $this->billingAddress = Address::find($this->checkoutSession['billing_address_id']);
            }
            
            if (isset($this->checkoutSession['payment_method_id'])) {
                $this->paymentMethod = PaymentMethod::find($this->checkoutSession['payment_method_id']);
            }
        } else {
            $this->checkoutSession = [];
        }
    }

    public function setAddresses(Address $shippingAddress, Address $billingAddress)
    {
        $this->shippingAddress = $shippingAddress;
        $this->billingAddress = $billingAddress;
        
        $this->checkoutSession['shipping_address_id'] = $shippingAddress->id;
        $this->checkoutSession['billing_address_id'] = $billingAddress->id;
        
        session(['checkout' => $this->checkoutSession]);
        
        return $this;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        
        $this->checkoutSession['payment_method_id'] = $paymentMethod->id;
        
        session(['checkout' => $this->checkoutSession]);
        
        return $this;
    }

    public function getCheckoutData()
    {
        return [
            'shipping_address' => $this->shippingAddress,
            'billing_address' => $this->billingAddress,
            'payment_method' => $this->paymentMethod,
        ];
    }

    public function getAddresses()
    {
        return [
            'shipping_address' => $this->shippingAddress,
            'billing_address' => $this->billingAddress,
        ];
    }

    public function completeOrder(array $data)
    {
        if (!$this->validateCheckoutData()) {
            throw new \Exception('Datos de checkout incompletos');
        }
        
        $cart = $this->cartService->getCart();
        
        // Check inventory before creating order
        foreach ($cart->items as $item) {
            if (!$this->inventoryService->checkAvailability($item->product_id, $item->quantity)) {
                throw new \Exception("El producto {$item->product->name} no tiene suficiente inventario");
            }
        }
        
        return DB::transaction(function () use ($cart) {
            // Create order
            $order = new Order([
                'user_id' => Auth::id(),
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'payment_method_id' => $this->paymentMethod->id,
                'shipping_address_id' => $this->shippingAddress->id,
                'billing_address_id' => $this->billingAddress->id,
                'subtotal' => $cart->subtotal,
                'tax' => $cart->tax ?? 0,
                'shipping_cost' => $cart->shipping_cost ?? 0,
                'discount' => $cart->discount ?? 0,
                'total' => $cart->total,
            ]);
            
            $order->save();
            
            // Create order items
            foreach ($cart->items as $item) {
                $orderItem = new OrderItem([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'options' => $item->options,
                    'subtotal' => $item->subtotal,
                ]);
                
                $order->items()->save($orderItem);
                
                // Update inventory
                $this->inventoryService->decrementStock($item->product_id, $item->quantity);
            }
            
            // Process payment
            $payment = $this->paymentService->processPayment($order, $this->paymentMethod);
            
            // Cleanup checkout session
            session()->forget('checkout');
            
            // Clear cart
            $this->cartService->clearCart();
            
            // Trigger order placed event
            event(new \App\Events\OrderPlaced($order));
            
            return $order;
        });
    }

    protected function validateCheckoutData()
    {
        return $this->shippingAddress && $this->billingAddress && $this->paymentMethod;
    }

    protected function generateOrderNumber()
    {
        $prefix = 'TIA-';
        $random = strtoupper(Str::random(6));
        $timestamp = now()->format('Ymd');
        
        return $prefix . $timestamp . '-' . $random;
    }
}