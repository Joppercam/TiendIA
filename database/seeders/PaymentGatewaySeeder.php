<?php
// database/seeders/PaymentGatewaySeeder.php
namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run()
    {
        // Webpay Plus (Transbank)
        PaymentGateway::create([
            'name' => 'Webpay Plus',
            'code' => 'webpay_plus',
            'description' => 'Paga con tarjeta de crédito o débito a través de Webpay Plus de Transbank',
            'is_active' => true,
            'is_default' => true,
            'logo' => 'payment-gateways/webpay.png',
            'position' => 0,
            'config' => [
                'environment' => 'testing', // 'testing' o 'production'
            ],
            'credentials' => [
                'commerce_code' => env('WEBPAY_COMMERCE_CODE', '597055555532'),
                'api_key' => env('WEBPAY_API_KEY', '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C'),
            ],
            'instructions' => 'Serás redirigido a la página de Webpay para completar tu pago.'
        ]);
        
        // Mercado Pago
        PaymentGateway::create([
            'name' => 'Mercado Pago',
            'code' => 'mercado_pago',
            'description' => 'Paga con tarjeta de crédito, débito o saldo de Mercado Pago',
            'is_active' => true,
            'is_default' => false,
            'logo' => 'payment-gateways/mercadopago.png',
            'position' => 1,
            'config' => [
                'environment' => 'testing', // 'testing' o 'production'
            ],
            'credentials' => [
                'access_token' => env('MERCADOPAGO_ACCESS_TOKEN', 'TEST-1234567890123456-012345-abcdefghijklmnopqrstuvwxyz-123456789'),
            ],
            'instructions' => 'Serás redirigido a la página de Mercado Pago para completar tu pago.'
        ]);
        
        // Khipu
        PaymentGateway::create([
            'name' => 'Khipu',
            'code' => 'khipu',
            // database/seeders/PaymentGatewaySeeder.php (continuación)
            'description' => 'Paga con transferencia bancaria a través de Khipu',
            'is_active' => true,
            'is_default' => false,
            'logo' => 'payment-gateways/khipu.png',
            'position' => 2,
            'config' => [
                'environment' => 'testing', // 'testing' o 'production'
            ],
            'credentials' => [
                'receiver_id' => env('KHIPU_RECEIVER_ID', '12345'),
                'secret_key' => env('KHIPU_SECRET_KEY', 'abcdefghijklmnopqrstuvwxyz123456789'),
            ],
            'instructions' => 'Serás redirigido a la página de Khipu para completar tu pago mediante transferencia bancaria.'
        ]);

        // Transferencia Bancaria Manual
        PaymentGateway::create([
            'name' => 'Transferencia Bancaria',
            'code' => 'bank_transfer',
            'description' => 'Paga mediante transferencia bancaria directa a nuestra cuenta',
            'is_active' => true,
            'is_default' => false,
            'logo' => 'payment-gateways/bank-transfer.png',
            'position' => 3,
            'config' => [],
            'credentials' => [
                'bank_name' => 'Banco de Chile',
                'account_type' => 'Cuenta Corriente',
                'account_number' => '000-1-23456-7',
                'rut' => '76.123.456-7',
                'account_name' => 'TiendIA SpA',
                'email' => 'pagos@tiendia.cl',
            ],
            'instructions' => 'Realiza una transferencia por el monto total de tu pedido a la cuenta bancaria indicada. Una vez realizada, envía el comprobante a pagos@tiendia.cl indicando tu número de orden.'
        ]);

        // Contra Entrega (Efectivo)
        PaymentGateway::create([
            'name' => 'Pago Contra Entrega',
            'code' => 'cash_on_delivery',
            'description' => 'Paga en efectivo al momento de recibir tu pedido',
            'is_active' => true,
            'is_default' => false,
            'logo' => 'payment-gateways/cash-on-delivery.png',
            'position' => 4,
            'min_amount' => 0,
            'max_amount' => 100000, // Límite de 100.000 CLP para pagos contra entrega
            'config' => [],
            'credentials' => [],
            'instructions' => 'Pago en efectivo al momento de recibir tu pedido. Asegúrate de tener el monto exacto disponible.'
        ]);

        // Flow
        PaymentGateway::create([
            'name' => 'Flow',
            'code' => 'flow',
            'description' => 'Paga con tarjeta de crédito, débito o saldo Flow',
            'is_active' => false, // Desactivado por defecto hasta configurar
            'is_default' => false,
            'logo' => 'payment-gateways/flow.png',
            'position' => 5,
            'config' => [
                'environment' => 'testing', // 'testing' o 'production'
            ],
            'credentials' => [
                'api_key' => env('FLOW_API_KEY', ''),
                'secret_key' => env('FLOW_SECRET_KEY', ''),
            ],
            'instructions' => 'Serás redirigido a la página de Flow para completar tu pago.'
        ]);

        // OnePay
        PaymentGateway::create([
            'name' => 'OnePay',
            'code' => 'onepay',
            'description' => 'Paga con OnePay escaneando un código QR',
            'is_active' => false, // Desactivado por defecto hasta configurar
            'is_default' => false,
            'logo' => 'payment-gateways/onepay.png',
            'position' => 6,
            'config' => [
                'environment' => 'testing', // 'testing' o 'production'
            ],
            'credentials' => [
                'api_key' => env('ONEPAY_API_KEY', ''),
                'shared_secret' => env('ONEPAY_SHARED_SECRET', ''),
            ],
            'instructions' => 'Escanea el código QR con la aplicación OnePay para completar tu pago.'
        ]);
    }
}