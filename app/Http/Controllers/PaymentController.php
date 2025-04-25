<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;
    
    public function __construct(PaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    /**
     * Muestra la página de selección de método de pago
     */
    public function index(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verificar que el usuario sea el propietario de la orden
        $this->authorize('view', $order);
        
        // Obtener pasarelas de pago activas
        $paymentGateways = $this->paymentService->getActiveGateways();
        
        return view('checkout.payment', [
            'order' => $order,
            'paymentGateways' => $paymentGateways
        ]);
    }
    
    /**
     * Inicia el proceso de pago con la pasarela seleccionada
     */
    public function processPayment(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verificar que el usuario sea el propietario de la orden
        $this->authorize('pay', $order);
        
        // Validar datos
        $validated = $request->validate([
            'gateway' => 'required|string',
            'payment_data' => 'sometimes|array'
        ]);
        
        // Verificar que la pasarela existe y está activa
        $gateway = PaymentGateway::where('code', $validated['gateway'])->where('is_active', true)->firstOrFail();
        
        // Inicializar el pago
        $response = $this->paymentService->initializePayment(
            $orderId,
            $validated['gateway'],
            $validated['payment_data'] ?? []
        );
        
        if (!$response['success']) {
            return back()->with('error', $response['message']);
        }
        
        // Si hay URL de redirección, redirigir al usuario
        if (isset($response['redirect_url'])) {
            return redirect()->away($response['redirect_url']);
        }
        
        // Para métodos como transferencia bancaria, mostrar instrucciones
        if ($validated['gateway'] === 'bank_transfer') {
            return view('checkout.bank-transfer', [
                'order' => $order,
                'payment' => $response['payment'],
                'bankInfo' => $response['payment']->gateway_response['bank_information'] ?? null,
                'instructions' => $response['payment']->gateway_response['instructions'] ?? null
            ]);
        }
        
        // En otros casos, mostrar página de procesamiento
        return view('checkout.processing', [
            'order' => $order,
            'payment' => $response['payment'],
            'gateway' => $gateway
        ]);
    }
    
    /**
     * Procesa el callback después del pago
     */
    public function handleCallback(Request $request, $gateway)
    {
        try {
            $data = $request->all();
            
            Log::info('Callback de pago recibido', [
                'gateway' => $gateway,
                'data' => $data
            ]);
            
            $response = $this->paymentService->processPayment($gateway, $data);
            
            if (!$response['success']) {
                return redirect()->route('checkout.payment.failure', ['gateway' => $gateway])
                    ->with('error', $response['message']);
            }
            
            $payment = $response['payment'];
            $order = $response['order'];
            
            // Redirigir según el estado del pago
            switch ($response['status']) {
                case 'completed':
                    return redirect()->route('checkout.payment.success', ['order' => $order->id]);
                case 'pending':
                    return redirect()->route('checkout.payment.pending', ['order' => $order->id]);
                case 'failed':
                    return redirect()->route('checkout.payment.failure', ['gateway' => $gateway])
                        ->with('error', 'El pago ha sido rechazado');
                default:
                    return redirect()->route('checkout.payment.status', ['order' => $order->id]);
            }
        } catch (\Exception $e) {
            Log::error('Error en callback de pago: ' . $e->getMessage(), [
                'gateway' => $gateway,
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('checkout.payment.failure', ['gateway' => $gateway])
                ->with('error', 'Ha ocurrido un error al procesar el pago');
        }
    }
    
    /**
     * Página de pago exitoso
     */
    public function success(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verificar que el usuario sea el propietario de la orden
        $this->authorize('view', $order);
        
        return view('checkout.success', [
            'order' => $order
        ]);
    }
    
    /**
     * Página de pago fallido
     */
    public function failure(Request $request, $gateway)
    {
        return view('checkout.failure', [
            'gateway' => $gateway,
            'error' => $request->session()->get('error')
        ]);
    }
    
    /**
     * Página de pago pendiente
     */
    public function pending(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verificar que el usuario sea el propietario de la orden
        $this->authorize('view', $order);
        
        return view('checkout.pending', [
            'order' => $order
        ]);
    }
    
    /**
     * Muestra el estado actual del pago
     */
    public function status(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verificar que el usuario sea el propietario de la orden
        $this->authorize('view', $order);
        
        $payment = $order->payments()->latest()->first();
        
        if ($payment) {
            // Verificar el estado actual del pago
            $this->paymentService->verifyPayment($payment->id);
            $payment->refresh();
        }
        
        return view('checkout.status', [
            'order' => $order,
            'payment' => $payment
        ]);
    }
}