<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $checkoutService;
    protected $paymentGatewayService;

    public function __construct(
        CartService $cartService, 
        CheckoutService $checkoutService,
        PaymentGatewayService $paymentGatewayService
    )
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
        $this->paymentGatewayService = $paymentGatewayService;
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
        
        // Obtener pasarelas de pago activas en lugar de métodos de pago
        $paymentGateways = $this->paymentGatewayService->getActiveGateways();
        
        return view('checkout.payment', [
            'cart' => $cart,
            'addresses' => $addresses,
            'paymentGateways' => $paymentGateways,
        ]);
    }

    public function processPayment(Request $request)
    {
        $validatedData = $request->validate([
            'gateway' => 'required|string|exists:payment_gateways,code',
            'payment_data' => 'sometimes|array',
        ]);
        
        // Guardar el método de pago seleccionado
        $gateway = PaymentGateway::where('code', $validatedData['gateway'])->where('is_active', true)->firstOrFail();
        $this->checkoutService->setPaymentGateway($gateway);
        
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
            // Completar la orden
            $order = $this->checkoutService->completeOrder($request->all());
            
            // Inicializar el pago según la pasarela seleccionada
            $paymentGateway = $this->checkoutService->getSelectedPaymentGateway();
            
            if ($paymentGateway) {
                $response = $this->paymentGatewayService->initializePayment(
                    $order->id,
                    $paymentGateway->code,
                    $request->input('payment_data', [])
                );
                
                if (!$response['success']) {
                    throw new \Exception($response['message'] ?? 'Error al inicializar el pago');
                }
                
                // Si hay URL de redirección, redirigir al usuario
                if (isset($response['redirect_url'])) {
                    return redirect()->away($response['redirect_url']);
                }
                
                // Para métodos como transferencia bancaria, mostrar instrucciones
                if ($paymentGateway->code === 'bank_transfer') {
                    return view('checkout.bank-transfer', [
                        'order' => $order,
                        'payment' => $response['payment'],
                        'bankInfo' => $response['payment']->gateway_response['bank_information'] ?? null,
                        'instructions' => $response['payment']->gateway_response['instructions'] ?? null
                    ]);
                }
            }
            
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