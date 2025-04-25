<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .company-info, .customer-info, .invoice-info {
            width: 30%;
        }
        .info-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('img/logo.png') }}" alt="TiendIA" class="logo">
            <div class="invoice-title">FACTURA</div>
            <div>{{ $invoice->invoice_number }}</div>
        </div>
        
        <div class="info-container">
            <div class="company-info">
                <div class="info-title">EMISOR</div>
                <div>TiendIA SpA</div>
                <div>RUT: 76.123.456-7</div>
                <div>Dirección: Av. Providencia 1234, Santiago</div>
                <div>Teléfono: +56 2 2123 4567</div>
                <div>Email: contacto@tiendia.cl</div>
            </div>
            
            <div class="customer-info">
                <div class="info-title">CLIENTE</div>
                <div>{{ $invoice->customer_details['name'] }}</div>
                @if(isset($invoice->customer_details['rut']) && !empty($invoice->customer_details['rut']))
                <div>RUT: {{ $invoice->customer_details['rut'] }}</div>
                @endif
                <div>Dirección: {{ $invoice->customer_details['address'] }}</div>
                <div>{{ $invoice->customer_details['city'] }}, {{ $invoice->customer_details['postal_code'] }}</div>
                <div>Email: {{ $invoice->customer_details['email'] }}</div>
                @if(isset($invoice->customer_details['phone']) && !empty($invoice->customer_details['phone']))
                <div>Teléfono: {{ $invoice->customer_details['phone'] }}</div>
                @endif
            </div>
            
            <div class="invoice-info">
                <div class="info-title">DETALLES</div>
                <div>Fecha de Emisión: {{ $invoice->invoice_date->format('d/m/Y') }}</div>
                <div>Fecha de Vencimiento: {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</div>
                <div>Estado: {{ ucfirst($invoice->status) }}</div>
                <div>Método de Pago: {{ $payment->paymentGateway->name }}</div>
                <div>Número de Orden: {{ $order->order_number }}</div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td class="text-right">${{ number_format($item['price'], 0, ',', '.') }}</td>
                    <td class="text-right">${{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right">Subtotal:</td>
                    <td class="text-right">${{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">IVA (19%):</td>
                    <td class="text-right">${{ number_format($invoice->tax, 0, ',', '.') }}</td>
                </tr>
                @if($order->shipping_cost > 0)
                <tr>
                    <td colspan="3" class="text-right">Envío:</td>
                    <td class="text-right">${{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($order->discount > 0)
                <tr>
                    <td colspan="3" class="text-right">Descuento:</td>
                    <td class="text-right">-${{ number_format($order->discount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3" class="text-right">Total:</td>
                    <td class="text-right">${{ number_format($invoice->total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        
        <div>
            <div class="info-title">NOTAS</div>
            <p>Esta factura es un documento electrónico válido según la normativa del Servicio de Impuestos Internos de Chile.</p>
            <p>Para cualquier consulta relacionada con esta factura, por favor contacta a nuestro equipo de atención al cliente a través de facturacion@tiendia.cl o al +56 2 2123 4567.</p>
        </div>
        
        <div class="footer">
            <p>TiendIA SpA - RUT: 76.123.456-7 - Giro: Comercio Electrónico</p>
            <p>Av. Providencia 1234, Santiago - Teléfono: +56 2 2123 4567 - Email: contacto@tiendia.cl</p>
            <p>Documento generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>