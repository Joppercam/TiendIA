<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Procesar webhook de pago
     */
    public function paymentWebhook(Request $request)
    {
        // Verificar firma del webhook (ejemplo para seguridad)
        if (!$this->verifySignature($request)) {
            return response()->json(['error' => 'Firma inválida'], 401);
        }

        $payload = $request->all();
        Log::info('Webhook de pago recibido', $payload);

        // Procesar según el tipo de evento
        $eventType = $payload['event'] ?? null;

        switch ($eventType) {
            case 'payment.succeeded':
                $this->handlePaymentSucceeded($payload);
                break;
            case 'payment.failed':
                $this->handlePaymentFailed($payload);
                break;
            case 'refund.processed':
                $this->handleRefundProcessed($payload);
                break;
            default:
                Log::warning('Tipo de evento de webhook no reconocido', ['event' => $eventType]);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Procesar webhook de inventario
     */
    public function inventoryWebhook(Request $request)
    {
        // Verificar autenticación mediante clave API
        if ($request->header('X-API-Key') !== config('services.inventory.api_key')) {
            return response()->json(['error' => 'Clave API inválida'], 401);
        }

        $payload = $request->all();
        Log::info('Webhook de inventario recibido', $payload);

        // Procesar actualización de inventario
        $this->handleInventoryUpdate($payload);

        return response()->json(['status' => 'success']);
    }

    /**
     * Verificar firma del webhook
     */
    private function verifySignature(Request $request)
    {
        $signature = $request->header('X-Webhook-Signature');
        $payload = $request->getContent();
        $secret = config('services.payment.webhook_secret');

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Manejar evento de pago exitoso
     */
    private function handlePaymentSucceeded(array $payload)
    {
        // Implementación para procesar pagos exitosos
        $orderId = $payload['data']['order_id'] ?? null;
        
        if ($orderId) {
            // Actualizar estado de la orden
            // Por ejemplo: app(OrderService::class)->markAsPaid($orderId);
        }
    }

    /**
     * Manejar evento de pago fallido
     */
    private function handlePaymentFailed(array $payload)
    {
        // Implementación para procesar pagos fallidos
    }

    /**
     * Manejar evento de reembolso procesado
     */
    private function handleRefundProcessed(array $payload)
    {
        // Implementación para procesar reembolsos
    }

    /**
     * Manejar actualización de inventario
     */
    private function handleInventoryUpdate(array $payload)
    {
        // Implementación para actualizar inventario desde sistema externo
        $items = $payload['items'] ?? [];
        
        foreach ($items as $item) {
            // Actualizar inventario
            // Por ejemplo: app(InventoryService::class)->updateExternalStock($item['sku'], $item['quantity']);
        }
    }
}