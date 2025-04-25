<?php

namespace App\Services\Payment;

use App\Interfaces\PaymentGatewayInterface;

class TransferBankGateway implements PaymentGatewayInterface
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function initializePayment(array $data)
    {
        // Para transferencia bancaria, solo creamos los datos necesarios
        // para mostrar la información de la cuenta bancaria al cliente
        return [
            'success' => true,
            'status' => 'pending',
            'gateway_reference' => 'TB-' . $data['order_id'] . '-' . time(),
            'bank_information' => [
                'bank_name' => $this->config['bank_name'] ?? 'Banco de Chile',
                'account_type' => $this->config['account_type'] ?? 'Cuenta Corriente',
                'account_number' => $this->config['account_number'] ?? '0000000000',
                'rut' => $this->config['rut'] ?? '76.123.456-7',
                'account_name' => $this->config['account_name'] ?? 'TiendIA SpA',
                'email' => $this->config['email'] ?? 'pagos@tiendia.cl'
            ],
            'instructions' => $this->config['instructions'] ?? 'Realizar una transferencia por el monto total y enviar el comprobante a pagos@tiendia.cl indicando el número de orden.'
        ];
    }
    
    public function processPayment(array $data)
    {
        // Para transferencia bancaria, el procesamiento es manual
        // Este método sería llamado cuando un administrador confirma el pago
        return [
            'success' => true,
            'status' => 'completed',
            'gateway_reference' => $data['gateway_reference'],
            'message' => 'Pago por transferencia bancaria confirmado manualmente'
        ];
    }
    
    public function verifyPayment($reference)
    {
        // No hay verificación automática para transferencia bancaria
        return [
            'success' => true,
            'status' => 'pending',
            'message' => 'Los pagos por transferencia bancaria requieren verificación manual'
        ];
    }
    
    public function processRefund(array $data)
    {
        // Para transferencia bancaria, los reembolsos son manuales
        return [
            'success' => true,
            'status' => 'pending',
            'message' => 'Solicitud de reembolso registrada. El reembolso debe procesarse manualmente.'
        ];
    }
    
    public function handleWebhook(array $data)
    {
        // Las transferencias bancarias no utilizan webhooks
        return [
            'success' => false,
            'message' => 'Las transferencias bancarias no utilizan webhooks'
        ];
    }
}