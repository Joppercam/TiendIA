<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\InvoiceService;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $invoiceService;
    
    public function __construct(PaymentGatewayService $paymentService, InvoiceService $invoiceService)
    {
        $this->paymentService = $paymentService;
        $this->invoiceService = $invoiceService;
        $this->middleware('auth');
        $this->middleware('role:admin,super-admin');
    }
    
    /**
     * Muestra el listado de pagos
     */
    public function index(Request $request)
    {
        $query = Payment::with(['order', 'paymentGateway']);
        
        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('gateway')) {
            $query->whereHas('paymentGateway', function($q) use ($request) {
                $q->where('code', $request->gateway);
            });
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('gateway_reference', 'like', "%{$search}%")
                  ->orWhereHas('order', function($sub) use ($search) {
                      $sub->where('order_number', 'like', "%{$search}%");
                  });
            });
        }
        
        // Ordenar
        $query->orderBy('created_at', 'desc');
        
        $payments = $query->paginate(15);
        $gateways = PaymentGateway::where('is_active', true)->get();
        
        return view('admin.payments.index', [
            'payments' => $payments,
            'gateways' => $gateways
        ]);
    }
    
    /**
     * Muestra los detalles de un pago
     */
    public function show($id)
    {
        $payment = Payment::with(['order', 'paymentGateway', 'transactions'])->findOrFail($id);
        
        return view('admin.payments.show', [
            'payment' => $payment
        ]);
    }
    
    /**
     * Verifica el estado actual de un pago
     */
    public function verify($id)
    {
        $payment = Payment::findOrFail($id);
        
        $response = $this->paymentService->verifyPayment($id);
        
        if (!$response['success']) {
            return back()->with('error', $response['message']);
        }
        
        return back()->with('success', 'El estado del pago ha sido verificado: ' . $response['status']);
    }
    
    /**
     * Marca un pago como completado manualmente
     */
    public function markAsPaid($id)
    {
        $payment = Payment::findOrFail($id);
        
        // Solo permitir marcar como pagados los pagos pendientes
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Solo se pueden marcar como pagados los pagos pendientes');
        }
        
        try {
            DB::beginTransaction();
            
            // Actualizar el pago
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    // app/Http/Controllers/Admin/PaymentController.php (continuación)
                    'manual_confirmation' => [
                        'user_id' => auth()->id(),
                        'timestamp' => now()->toIso8601String(),
                        'notes' => 'Pago confirmado manualmente por administrador'
                    ]
                ])
            ]);
            
            // Actualizar la orden
            $payment->order->update([
                'payment_status' => 'completed',
                'status' => 'processing'
            ]);
            
            // Generar factura si no existe
            if (!$payment->invoice) {
                $this->invoiceService->generateInvoice($payment->order, $payment);
            }
            
            DB::commit();
            
            return back()->with('success', 'El pago ha sido marcado como completado');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al marcar pago como completado: ' . $e->getMessage(), [
                'payment_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error al marcar el pago como completado: ' . $e->getMessage());
        }
    }
    
    /**
     * Procesa un reembolso
     */
    public function refund(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        
        // Validar datos
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:1|max:' . $payment->amount,
            'reason' => 'nullable|string|max:255'
        ]);
        
        $response = $this->paymentService->processRefund(
            $id,
            $validated['amount'] ?? null,
            $validated['reason'] ?? null
        );
        
        if (!$response['success']) {
            return back()->with('error', $response['message']);
        }
        
        return back()->with('success', 'El reembolso ha sido procesado correctamente');
    }
    
    /**
     * Descarga la factura de un pago
     */
    public function downloadInvoice($id)
    {
        $payment = Payment::findOrFail($id);
        
        if (!$payment->invoice) {
            return back()->with('error', 'Este pago no tiene factura asociada');
        }
        
        try {
            $pdf = $this->invoiceService->getInvoicePdf($payment->invoice);
            
            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $payment->invoice->invoice_number . '.pdf"');
        } catch (\Exception $e) {
            Log::error('Error al descargar factura: ' . $e->getMessage(), [
                'invoice_id' => $payment->invoice->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error al descargar la factura: ' . $e->getMessage());
        }
    }
}