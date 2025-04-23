<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\PaymentMethod;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $checkoutService;

    public function __construct(CartService $cartService, CheckoutService $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
        $this->middleware('auth')->except(['guest']);
        $this->middleware('checkout');
    }

    public function index()
    {
        $cart = $this->cartService->getCart();
        
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío, no puedes proceder al checkout.');
        }
        
        $addresses = Auth::check() ? Auth::user()->addresses : collect([]);
        
        return view('checkout.index', [
            'cart' => $cart,
            'addresses' => $addresses,
        ]);
    }

    public function address(Request $request)
    {
        $validatedData = $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'same_billing_address' => 'sometimes|boolean',
        ]);

        $shippingAddress = Address::findOrFail($validatedData['shipping_address_id']);
        
        if (isset($validatedData['same_billing_address']) && $validatedData['same_billing_address']) {
            $billingAddress = $shippingAddress;
        } else {
            $billingAddress = Address::findOrFail($validatedData['billing_address_id']);
        }
        
        $this->checkoutService->setAddresses($shippingAddress, $billingAddress);
        
        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        $cart = $this->cartService->getCart();
        $addresses = $this->checkoutService->getAddresses();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        
        return view('checkout.payment', [
            'cart' => $cart,
            'addresses' => $addresses,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function processPayment(Request $request)
    {
        $validatedData = $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);
        
        $this->checkoutService->setPaymentMethod(
            PaymentMethod::findOrFail($validatedData['payment_method_id'])
        );
        
        return redirect()->route('checkout.review');
    }

    public function review()
    {
        $cart = $this->cartService->getCart();
        $checkoutData = $this->checkoutService->getCheckoutData();
        
        return view('checkout.review', [
            'cart' => $cart,
            'checkoutData' => $checkoutData,
        ]);
    }

    public function complete(Request $request)
    {
        try {
            $order = $this->checkoutService->completeOrder($request->all());
            
            return redirect()->route('checkout.success', ['order' => $order->id]);
        } catch (\Exception $e) {
            return redirect()->route('checkout.review')
                ->with('error', 'Error al procesar la orden: ' . $e->getMessage());
        }
    }

    public function success($orderId)
    {
        $order = Auth::user()->orders()->findOrFail($orderId);
        
        return view('checkout.success', [
            'order' => $order,
        ]);
    }

    public function guest()
    {
        $cart = $this->cartService->getCart();
        
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío, no puedes proceder al checkout.');
        }
        
        return view('checkout.guest', [
            'cart' => $cart,
        ]);
    }
}