<?php

namespace App\Services\Payment;

use App\Interfaces\PaymentGatewayInterface;
use Khipu\ApiClient;
use Khipu\Client\Configuration;
use Khipu\Client\ApiException;

class KhipuGateway implements PaymentGatewayInterface
{
    protected $client;
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        
        // Configurar cliente Khipu
        $configuration = new Configuration();
        $configuration->setSecret($config['secret_key'] ?? config('services.khipu.secret_key'));
        $configuration->setReceiverId($config['receiver_id'] ?? config('services.khipu.receiver_id'));
        
        $this->client = new ApiClient($configuration);
    }

    public function initializePayment(array $data)
    {
        try {
            $payments = $this->client->getPaymentsApi();
            
            $response = $payments->paymentsPost(
                $data['subject'] ?? 'Orden #' . $data['order_id'],
                'CLP',
                $data['amount'],
                [
                    'transaction_id' => (string) $data['order_id'],
                    'return_url' => $data['return_url'],
                    'cancel_url' => $data['cancel_url'],
                    'notify_url' => $data['notify_url'],
                    'notify_api_version' => '1.3'
                ]
            );
            
            return [
                'success' => true,
                'payment_id' => $response->getPaymentId(),
                'redirect_url' => $response->getPaymentUrl(),
                'gateway_reference' => $response->getPaymentId()
            ];
        } catch (ApiException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function processPayment(array $data)
    {
        // Para Khipu, esto se maneja típicamente a través del webhook
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
            $payments = $this->client->getPaymentsApi();
            $response = $payments->paymentsGet($reference);
            
            $status = 'pending';
            if ($response->getStatus() == 'done') {
                $status = 'completed';
            } elseif (in_array($response->getStatus(), ['expired', 'rejected'])) {
                $status = 'failed';
            }
            
            return [
                'success' => true,
                'status' => $status,
                'gateway_response' => [
                    'payment_id' => $response->getPaymentId(),
                    'payment_url' => $response->getPaymentUrl(),
                    'simplified_transfer_url' => $response->getSimplifiedTransferUrl(),
                    'transfer_url' => $response->getTransferUrl(),
                    'status' => $response->getStatus(),
                    'subject' => $response->getSubject(),
                    'amount' => $response->getAmount(),
                    'transaction_id' => $response->getTransactionId()
                ]
            ];
        } catch (ApiException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function processRefund(array $data)
    {
        // Khipu no soporta reembolsos a través de la API, se deben hacer manualmente
        return [
            'success' => false,
            'message' => 'Khipu no soporta reembolsos automáticos. Por favor, realice el reembolso manualmente desde el panel de Khipu.'
        ];
    }
    
    public function handleWebhook(array $data)
    {
        if (isset($data['api_version']) && $data['api_version'] == '1.3' && isset($data['notification_token'])) {
            try {
                $payments = $this->client->getPaymentsApi();
                $response = $payments->paymentsGet(null, $data['notification_token']);
                
                $status = 'pending';
                if ($response->getStatus() == 'done') {
                    $status = 'completed';
                } elseif (in_array($response->getStatus(), ['expired', 'rejected'])) {
                    $status = 'failed';
                }
                
                return [
                    'success' => true,
                    'status' => $status,
                    'order_id' => $response->getTransactionId(),
                    'amount' => $response->getAmount(),
                    'gateway_reference' => $response->getPaymentId(),
                    'gateway_response' => [
                        'payment_id' => $response->getPaymentId(),
                        'status' => $response->getStatus(),
                        'subject' => $response->getSubject()
                    ]
                ];
            } catch (ApiException $e) {
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