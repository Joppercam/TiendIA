@extends('admin.layouts.app')

@section('title', 'Detalles del Pedido #' . $order->order_number)

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            
            @if(!$order->cancelled_at)
                <a href="{{ route('admin.shipments.create', $order) }}" class="btn btn-primary">
                    <i class="fas fa-shipping-fast"></i> Crear Envío
                </a>
                
                <a href="{{ route('admin.orders.generate-invoice', $order) }}" class="btn btn-info">
                    <i class="fas fa-file-invoice"></i> Generar Factura
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Pedido #{{ $order->order_number }}
                        <span class="badge ml-2" style="background-color: {{ $order->status->color }}">
                            {{ $order->status->name }}
                        </span>
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Cliente:</strong> 
                                @if($order->user)
                                    {{ $order->user->name }} ({{ $order->user->email }})
                                @else
                                    {{ $order->guest_name }} ({{ $order->guest_email }})
                                @endif
                            </p>
                            <p><strong>Estado de pago:</strong> {{ ucfirst($order->payment_status) }}</p>
                            <p><strong>Estado de envío:</strong> {{ ucfirst($order->shipping_status) }}</p>
                        </div>
                        <div class="col-md-6">
                            @if($order->paid_at)
                                <p><strong>Pagado el:</strong> {{ $order->paid_at->format('d/m/Y H:i') }}</p>
                            @endif
                            
                            @if($order->shipped_at)
                                <p><strong>Enviado el:</strong> {{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                            @endif
                            
                            @if($order->delivered_at)
                                <p><strong>Entregado el:</strong> {{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                            @endif
                            
                            @if($order->cancelled_at)
                                <p><strong>Cancelado el:</strong> {{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    <h5 class="mt-4">Productos</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->product_name }}
                                            @if($item->product)
                                                <a href="{{ route('admin.products.edit', $item->product) }}" class="ml-2">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif
                                            
                                            @if($item->options)
                                                <br>
                                                <small>
                                                    @foreach(json_decode($item->options, true) ?? [] as $key => $value)
                                                        <span class="badge badge-light">{{ $key }}: {{ $value }}</span>
                                                    @endforeach
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $order->currency }} {{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $order->currency }} {{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                    <td>{{ $order->currency }} {{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                @if($order->discount > 0)
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Descuento:</strong></td>
                                        <td>-{{ $order->currency }} {{ number_format($order->discount, 2) }}</td>
                                    </tr>
                                @endif
                                @if($order->tax > 0)
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Impuestos:</strong></td>
                                        <td>{{ $order->currency }} {{ number_format($order->tax, 2) }}</td>
                                    </tr>
                                @endif
                                @if($order->shipping_cost > 0)
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Envío:</strong></td>
                                        <td>{{ $order->currency }} {{ number_format($order->shipping_cost, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                    <td><strong>{{ $order->currency }} {{ number_format($order->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    @if($order->notes)
                        <div class="mt-4">
                            <h5>Notas</h5>
                            <p>{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            @if($order->shipment)
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Información de Envío</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Estado:</strong> {{ $order->shipment->status }}</p>
                        <p><strong>Compañía:</strong> {{ $order->shipment->shipping_company }}</p>
                        
                        @if($order->shipment->tracking_number)
                            <p><strong>Número de seguimiento:</strong> {{ $order->shipment->tracking_number }}</p>
                        @endif
                        
                        @if($order->shipment->shipped_at)
                            <p><strong>Enviado el:</strong> {{ $order->shipment->shipped_at->format('d/m/Y H:i') }}</p>
                        @endif
                        
                        @if($order->shipment->delivered_at)
                            <p><strong>Entregado el:</strong> {{ $order->shipment->delivered_at->format('d/m/Y H:i') }}</p>
                        @endif
                        
                        <a href="{{ route('admin.shipments.show', $order->shipment) }}" class="btn btn-info">
                            <i class="fas fa-shipping-fast"></i> Ver Detalles de Envío
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actualizar Estado</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="order_status_id">Estado del Pedido</label>
                            <select name="order_status_id" id="order_status_id" class="form-control">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ $order->order_status_id == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notas (opcional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Actualizar Estado</button>
                    </form>
                    
                    @if(!$order->cancelled_at)
                        <hr>
                        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres cancelar este pedido?');">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="reason">Motivo de Cancelación</label>
                                <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-danger">Cancelar Pedido</button>
                        </form>
                    @endif
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Dirección de Envío</h3>
                </div>
                <div class="card-body">
                    @if($order->address)
                        <address>
                            <strong>{{ $order->address->first_name }} {{ $order->address->last_name }}</strong><br>
                            {{ $order->address->address_line_1 }}<br>
                            
                            @if($order->address->address_line_2)
                                {{ $order->address->address_line_2 }}<br>
                            @endif
                            
                            {{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->zip_code }}<br>
                            {{ $order->address->country }}<br>
                            
                            @if($order->address->phone)
                                <abbr title="Teléfono">Tel:</abbr> {{ $order->address->phone }}
                            @endif
                        </address>
                    @else
                        <p>No hay dirección de envío registrada para este pedido.</p>
                    @endif
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Método de Pago</h3>
                </div>
                <div class="card-body">
                    @if($order->paymentMethod)
                        <p><strong>Método:</strong> {{ $order->paymentMethod->name }}</p>
                        <p><strong>Estado:</strong> {{ ucfirst($order->payment_status) }}</p>
                        
                        @if($order->paid_at)
                            <p><strong>Pagado el:</strong> {{ $order->paid_at->format('d/m/Y H:i') }}</p>
                        @endif
                    @else
                        <p>No hay información de pago disponible para este pedido.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection