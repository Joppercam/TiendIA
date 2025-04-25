<?php

namespace App\Services\Payment;

use App\Interfaces\PaymentGatewayInterface;
use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;

class WebpayPlusGateway implements PaymentGatewayInterface
{
    protected $transaction;
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        
        // Configuración según ambiente
        if (isset($config['commerce_code']) && isset($config['api_key'])) {
            WebpayPlus::configureForProduction(
                $config['commerce_code'],
                $config['api_key']
            );
        } else {
            WebpayPlus::configureForTesting();
        }
        
        $this->transaction = new Transaction();
    }

    public function initializePayment(array $data)
    {
        $buyOrder = $data['order_id'];
        $sessionId = $data['session_id'];
        $amount = $data['amount'];
        $returnUrl = $data['return_url'];
        
        try {
            $response = $this->transaction->create($buyOrder, $sessionId, $amount, $returnUrl);
            
            return [
                'success' => true,
                'redirect_url' => $response->getUrl(),
                'token' => $response->getToken(),
                'gateway_reference' => $response->getToken()
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
        $token = $data['token'];
        
        try {
            $response = $this->transaction->commit($token);
            
            if ($response->isApproved()) {
                return [
                    'success' => true,
                    'status' => 'completed',
                    'gateway_reference' => $response->getBuyOrder(),
                    'gateway_response' => [
                        'authorization_code' => $response->getAuthorizationCode(),
                        'card_number' => $response->getCardDetail()['card_number'],
                        'transaction_date' => $response->getTransactionDate(),
                        'payment_type_code' => $response->getPaymentTypeCode(),
                        'amount' => $response->getAmount(),
                        'response_code' => $response->getResponseCode()
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'status' => 'failed',
                    'message' => 'Transacción rechazada',
                    'gateway_response' => [
                        'response_code' => $response->getResponseCode()
                    ]
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function verifyPayment($reference)
    {
        try {
            $response = $this->transaction->status($reference);
            
            return [
                'success' => true,
                'status' => $response->isApproved() ? 'completed' : 'failed',
                'gateway_response' => [
                    'status' => $response->getStatus(),
                    'amount' => $response->getAmount(),
                    'buy_order' => $response->getBuyOrder(),
                    'payment_type_code' => $response->getPaymentTypeCode()
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
        $token = $data['token'];
        $amount = $data['amount'];
        
        try {
            $response = $this->transaction->refund($token, $amount);
            
            return [
                'success' => true,
                'status' => 'refunded',
                'gateway_response' => [
                    'type' => $response->getType(),
                    'authorization_code' => $response->getAuthorizationCode(),
                    'response_code' => $response->getResponseCode(),
                    'authorization_date' => $response->getAuthorizationDate()
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
        // Webpay no utiliza webhooks, se maneja todo a través de la redirección
        return [
            'success' => true,
            'message' => 'Webpay no procesa webhooks'
        ];
    }
}