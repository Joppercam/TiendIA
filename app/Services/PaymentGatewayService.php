<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentGatewayFactory;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    /**
     * Obtiene todas las pasarelas de pago activas
     */
    public function getActiveGateways()
    {
        return PaymentGateway::where('is_active', true)
            ->orderBy('position', 'asc')
            ->get();
    }
    
    /**
     * Inicializa una transacción de pago
     */
    public function initializePayment($orderId, $gatewayCode, array $additionalData = [])
    {
        try {
            $order = \App\Models\Order::findOrFail($orderId);
            $gateway = PaymentGateway::where('code', $gatewayCode)->where('is_active', true)->firstOrFail();
            
            $gatewayInstance = PaymentGatewayFactory::create($gatewayCode);
            
            // Preparar los datos base para la pasarela
            $data = array_merge([
                'order_id' => $order->id,
                'amount' => $order->total,
                'session_id' => session()->getId(),
                'return_url' => route('checkout.payment.callback', ['gateway' => $gatewayCode]),
                'cancel_url' => route('checkout.payment.cancel', ['gateway' => $gatewayCode]),
                // app/Services/PaymentGatewayService.php (continuación)
                'notify_url' => route('api.webhooks.payment', ['gateway' => $gatewayCode]),
                'success_url' => route('checkout.payment.success', ['gateway' => $gatewayCode]),
                'failure_url' => route('checkout.payment.failure', ['gateway' => $gatewayCode]),
                'pending_url' => route('checkout.payment.pending', ['gateway' => $gatewayCode]),
                'subject' => 'Orden #' . $order->id . ' - TiendIA'
            ], $additionalData);
            
            // Inicializar el pago con la pasarela
            $response = $gatewayInstance->initializePayment($data);
            
            if (!$response['success']) {
                throw new Exception($response['message'] ?? 'Error al inicializar el pago');
            }
            
            // Registrar el pago en la base de datos
            DB::beginTransaction();
            
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_gateway_id' => $gateway->id,
                'amount' => $order->total,
                'currency' => 'CLP',
                'status' => 'pending',
                'gateway_reference' => $response['gateway_reference'] ?? null,
                'gateway_response' => $response,
                'notes' => 'Pago inicializado mediante ' . $gateway->name
            ]);
            
            // Actualizar estado de la orden
            $order->update(['payment_status' => 'pending']);
            
            DB::commit();
            
            return [
                'success' => true,
                'payment_id' => $payment->id,
                'redirect_url' => $response['redirect_url'] ?? null,
                'gateway_reference' => $response['gateway_reference'] ?? null,
                'payment' => $payment
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al inicializar pago: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'gateway' => $gatewayCode,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Procesa una transacción de pago (callback)
     */
    public function processPayment($gatewayCode, array $data)
    {
        try {
            $gatewayInstance = PaymentGatewayFactory::create($gatewayCode);
            $response = $gatewayInstance->processPayment($data);
            
            if (!$response['success']) {
                throw new Exception($response['message'] ?? 'Error al procesar el pago');
            }
            
            // Buscar el pago por referencia de la pasarela
            $gatewayReference = $response['gateway_reference'] ?? $data['gateway_reference'] ?? null;
            $payment = Payment::where('gateway_reference', $gatewayReference)->first();
            
            if (!$payment) {
                // Si no se encuentra por referencia, intentar buscar por order_id si está disponible
                $orderId = $response['order_id'] ?? $data['order_id'] ?? null;
                if ($orderId) {
                    $gateway = PaymentGateway::where('code', $gatewayCode)->first();
                    $payment = Payment::where('order_id', $orderId)
                        ->where('payment_gateway_id', $gateway->id)
                        ->latest()
                        ->first();
                }
                
                if (!$payment) {
                    throw new Exception('No se encontró el pago asociado');
                }
            }
            
            DB::beginTransaction();
            
            // Registrar la transacción
            PaymentTransaction::create([
                'payment_id' => $payment->id,
                'type' => 'payment',
                'amount' => $payment->amount,
                'reference' => $gatewayReference,
                'status' => $response['status'],
                'gateway_response' => $response['gateway_response'] ?? $response,
                'notes' => 'Transacción procesada por ' . $gatewayCode
            ]);
            
            // Actualizar el pago
            $payment->update([
                'status' => $response['status'],
                'gateway_response' => array_merge($payment->gateway_response ?? [], ['processing' => $response]),
                'paid_at' => $response['status'] === 'completed' ? now() : null
            ]);
            
            // Actualizar la orden
            $order = $payment->order;
            $order->update([
                'payment_status' => $response['status'],
                'status' => $response['status'] === 'completed' ? 'processing' : $order->status
            ]);
            
            // Si el pago fue completado, generar factura
            if ($response['status'] === 'completed') {
                app(InvoiceService::class)->generateInvoice($order, $payment);
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'status' => $response['status'],
                'payment' => $payment,
                'order' => $order
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar pago: ' . $e->getMessage(), [
                'gateway' => $gatewayCode,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Verifica el estado de un pago
     */
    public function verifyPayment($paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);
            $gateway = $payment->paymentGateway;
            
            $gatewayInstance = PaymentGatewayFactory::create($gateway->code);
            $response = $gatewayInstance->verifyPayment($payment->gateway_reference);
            
            if (!$response['success']) {
                throw new Exception($response['message'] ?? 'Error al verificar el pago');
            }
            
            // Si el estado ha cambiado, actualizar el pago
            if ($response['status'] !== $payment->status) {
                DB::beginTransaction();
                
                // Registrar la transacción
                PaymentTransaction::create([
                    'payment_id' => $payment->id,
                    'type' => 'verification',
                    'amount' => $payment->amount,
                    'reference' => $payment->gateway_reference,
                    'status' => $response['status'],
                    'gateway_response' => $response['gateway_response'] ?? $response,
                    'notes' => 'Verificación de pago en ' . $gateway->name
                ]);
                
                // Actualizar el pago
                $payment->update([
                    'status' => $response['status'],
                    'gateway_response' => array_merge($payment->gateway_response ?? [], ['verification' => $response]),
                    'paid_at' => $response['status'] === 'completed' ? now() : $payment->paid_at
                ]);
                
                // Actualizar la orden
                $order = $payment->order;
                $order->update([
                    'payment_status' => $response['status'],
                    'status' => $response['status'] === 'completed' ? 'processing' : $order->status
                ]);
                
                // Si el pago fue completado, generar factura
                if ($response['status'] === 'completed' && !$payment->invoice) {
                    app(InvoiceService::class)->generateInvoice($order, $payment);
                }
                
                DB::commit();
            }
            
            return [
                'success' => true,
                'status' => $response['status'],
                'payment' => $payment->fresh(),
                'verification_response' => $response
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al verificar pago: ' . $e->getMessage(), [
                'payment_id' => $paymentId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error al verificar el pago: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Procesa un reembolso
     */
    public function processRefund($paymentId, $amount = null, $reason = null)
    {
        try {
            $payment = Payment::findOrFail($paymentId);
            
            // Verificar que el pago esté completado
            if ($payment->status !== 'completed') {
                throw new Exception('No se puede reembolsar un pago que no está completado');
            }
            
            $gateway = $payment->paymentGateway;
            $gatewayInstance = PaymentGatewayFactory::create($gateway->code);
            
            // Si no se especifica monto, reembolsar el total
            $refundAmount = $amount ?? $payment->amount;
            
            $data = [
                'payment_id' => $payment->id,
                'gateway_reference' => $payment->gateway_reference,
                'token' => $payment->gateway_reference,
                'amount' => $refundAmount,
                'reason' => $reason ?? 'Reembolso solicitado'
            ];
            
            $response = $gatewayInstance->processRefund($data);
            
            DB::beginTransaction();
            
            // Registrar la transacción de reembolso
            PaymentTransaction::create([
                'payment_id' => $payment->id,
                'type' => 'refund',
                'amount' => $refundAmount,
                'reference' => $response['gateway_reference'] ?? ($response['gateway_response']['id'] ?? null),
                'status' => $response['success'] ? 'completed' : 'failed',
                'gateway_response' => $response['gateway_response'] ?? $response,
                'notes' => $reason ?? 'Reembolso procesado'
            ]);
            
            // Actualizar el pago si es reembolso total
            if ($refundAmount >= $payment->amount) {
                $payment->update([
                    'status' => 'refunded',
                    'gateway_response' => array_merge($payment->gateway_response ?? [], ['refund' => $response])
                ]);
                
                // Actualizar la orden
                $payment->order->update([
                    'payment_status' => 'refunded',
                    'status' => 'refunded'
                ]);
            } else {
                // Si es reembolso parcial
                $payment->update([
                    'status' => 'partially_refunded',
                    'gateway_response' => array_merge($payment->gateway_response ?? [], ['refund' => $response])
                ]);
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'status' => $payment->status,
                'amount_refunded' => $refundAmount,
                'payment' => $payment->fresh()
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar reembolso: ' . $e->getMessage(), [
                'payment_id' => $paymentId,
                'amount' => $amount,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error al procesar el reembolso: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Procesa un webhook de pasarela de pago
     */
    public function handleWebhook($gatewayCode, array $data)
    {
        try {
            $gatewayInstance = PaymentGatewayFactory::create($gatewayCode);
            $response = $gatewayInstance->handleWebhook($data);
            
            if (!$response['success']) {
                // Si es un webhook no reconocido, posiblemente no sea un error
                Log::info('Webhook no procesado: ' . ($response['message'] ?? 'Sin mensaje'), [
                    'gateway' => $gatewayCode,
                    'data' => $data
                ]);
                
                return [
                    'success' => false,
                    'message' => $response['message'] ?? 'Webhook no procesado'
                ];
            }
            
            // Si no hay orden o referencia, no podemos procesar
            if (empty($response['order_id']) && empty($response['gateway_reference'])) {
                throw new Exception('El webhook no contiene ID de orden o referencia');
            }
            
            // Buscar el pago
            $payment = null;
            if (!empty($response['gateway_reference'])) {
                $payment = Payment::where('gateway_reference', $response['gateway_reference'])->first();
            }
            
            if (!$payment && !empty($response['order_id'])) {
                $gateway = PaymentGateway::where('code', $gatewayCode)->first();
                $payment = Payment::where('order_id', $response['order_id'])
                    ->where('payment_gateway_id', $gateway->id)
                    ->latest()
                    ->first();
            }
            
            if (!$payment) {
                throw new Exception('No se encontró el pago asociado al webhook');
            }
            
            DB::beginTransaction();
            
            // Registrar la transacción
            PaymentTransaction::create([
                'payment_id' => $payment->id,
                'type' => 'webhook',
                'amount' => $response['amount'] ?? $payment->amount,
                'reference' => $response['gateway_reference'] ?? $payment->gateway_reference,
                'status' => $response['status'],
                'gateway_response' => $response['gateway_response'] ?? $response,
                'notes' => 'Webhook recibido de ' . $gatewayCode
            ]);
            
            // Actualizar el pago si el estado ha cambiado
            if ($response['status'] !== $payment->status) {
                $payment->update([
                    'status' => $response['status'],
                    'gateway_response' => array_merge($payment->gateway_response ?? [], ['webhook' => $response]),
                    'paid_at' => $response['status'] === 'completed' ? now() : $payment->paid_at
                ]);
                
                // Actualizar la orden
                $order = $payment->order;
                $order->update([
                    'payment_status' => $response['status'],
                    'status' => $response['status'] === 'completed' ? 'processing' : $order->status
                ]);
                
                // Si el pago fue completado, generar factura
                if ($response['status'] === 'completed' && !$payment->invoice) {
                    app(InvoiceService::class)->generateInvoice($order, $payment);
                }
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'status' => $response['status'],
                'payment' => $payment->fresh()
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar webhook: ' . $e->getMessage(), [
                'gateway' => $gatewayCode,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error al procesar el webhook: ' . $e->getMessage()
            ];
        }
    }
}