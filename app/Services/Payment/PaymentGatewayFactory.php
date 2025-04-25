<?php

namespace App\Services\Payment;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\PaymentGateway;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function create(string $gatewayCode, array $config = null): PaymentGatewayInterface
    {
        // Si no se proporcionan configuraciones, intenta obtenerlas del modelo
        if ($config === null) {
            $gatewayModel = PaymentGateway::where('code', $gatewayCode)->where('is_active', true)->first();
            
            if (!$gatewayModel) {
                throw new InvalidArgumentException("Pasarela de pago '$gatewayCode' no encontrada o no está activa");
            }
            
            $config = $gatewayModel->credentials ?? [];
        }
        
        switch ($gatewayCode) {
            case 'webpay_plus':
                return new WebpayPlusGateway($config);
            case 'mercado_pago':
                return new MercadoPagoGateway($config);
            case 'khipu':
                return new KhipuGateway($config);
            case 'bank_transfer':
                return new TransferBankGateway($config);
            default:
                throw new InvalidArgumentException("Pasarela de pago '$gatewayCode' no soportada");
        }
    }
}