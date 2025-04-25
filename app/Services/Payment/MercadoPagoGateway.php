<?php

namespace App\Services\Payment;

use App\Interfaces\PaymentGatewayInterface;
use MercadoPago\SDK;
use MercadoPago\Payment;
use MercadoPago\Refund;

class MercadoPagoGateway implements PaymentGatewayInterface
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        
        // Configurar SDK de Mercado Pago
        SDK::setAccessToken($config['access_token'] ?? config('services.mercadopago.access_token'));
    }

    public function initializePayment(array $data)
    {
        $preferenceData = [
            'items' => [
                [
                    'title' => 'Orden #' . $data['order_id'],
                    'quantity' => 1,
                    'currency_id' => 'CLP',
                    'unit_price' => (float) $data['amount']
                ]
            ],
            'back_urls' => [
                'success' => $data['success_url'],
                'failure' => $data['failure_url'],
                'pending' => $data['pending_url']
            ],
            'auto_return' => 'approved',
            'external_reference' => (string) $data['order_id'],
            'notification_url' => $data['notification_url']
        ];
        
        try {
            $preference = new \MercadoPago\Preference();
            
            foreach ($preferenceData as $key => $value) {
                $preference->$key = $value;
            }
            
            $preference->save();
            
            return [
                'success' => true,
                'preference_id' => $preference->id,
                'redirect_url' => $preference->init_point,
                'gateway_reference' => $preference->id
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function processPayment(array $data)
    {
        // Para Mercado Pago, esto se maneja típicamente a través del webhook
        // o la redirección después del pago
        return [
            'success' => true,
            'status' => 'pending',
            'message' => 'Esperando confirmación del pago'
        ];
    }
    
    public function verifyPayment($reference)
    {
        try {
            $payment = Payment::find_by_id($reference);
            
            $status = 'pending';
            if ($payment->status == 'approved') {
                $status = 'completed';
            } elseif (in_array($payment->status, ['rejected', 'cancelled'])) {
                $status = 'failed';
            }
            
            return [
                'success' => true,
                'status' => $status,
                'gateway_response' => [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'status_detail' => $payment->status_detail,
                    'payment_method_id' => $payment->payment_method_id,
                    'payment_type_id' => $payment->payment_type_id,
                    'transaction_amount' => $payment->transaction_amount,
                    'external_reference' => $payment->external_reference
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function processRefund(array $data)
    {
        try {
            $refund = new Refund();
            $refund->payment_id = $data['payment_id'];
            $refund->amount = $data['amount'];
            $refund->save();
            
            return [
                'success' => true,
                'status' => 'refunded',
                'gateway_response' => [
                    'id' => $refund->id,
                    'payment_id' => $refund->payment_id,
                    'amount' => $refund->amount,
                    'status' => $refund->status
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function handleWebhook(array $data)
    {
        if (isset($data['type']) && $data['type'] == 'payment' && isset($data['data']['id'])) {
            $paymentId = $data['data']['id'];
            
            try {
                $payment = Payment::find_by_id($paymentId);
                
                $status = 'pending';
                if ($payment->status == 'approved') {
                    $status = 'completed';
                } elseif (in_array($payment->status, ['rejected', 'cancelled'])) {
                    $status = 'failed';
                }
                
                return [
                    'success' => true,
                    'status' => $status,
                    'order_id' => $payment->external_reference,
                    'amount' => $payment->transaction_amount,
                    'gateway_reference' => $payment->id,
                    'gateway_response' => [
                        'id' => $payment->id,
                        'status' => $payment->status,
                        'status_detail' => $payment->status_detail,
                        'payment_method_id' => $payment->payment_method_id,
                        'payment_type_id' => $payment->payment_type_id
                    ]
                ];
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Evento de webhook no soportado'
        ];
    }
}