@extends('layouts.app')

@section('title', 'Detalles del Pedido #' . $order->order_number)

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('shop.orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Mis Pedidos
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pedido #{{ $order->order_number }}</h5>
            <span class="badge" style="background-color: {{ $order->status->color }}; color: white;">
                {{ $order->status->name }}
            </span>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Información del Pedido</h6>
                    <p class="mb-1">
                        <strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="mb-1">
                        <strong>Estado del pago:</strong> {{ ucfirst($order->payment_status) }}
                    </p>
                    <p class="mb-1">
                        <strong>Estado del envío:</strong> {{ ucfirst($order->shipping_status) }}
                    </p>
                    @if($order->paid_at)
                        <p class="mb-1">
                            <strong>Pagado el:</strong> {{ $order->paid_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                    
                    @if($order->shipped_at)
                        <p class="mb-1">
                            <strong>Enviado el:</strong> {{ $order->shipped_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                    
                    @if($order->delivered_at)
                        <p class="mb-1">
                            <strong>Entregado el:</strong> {{ $order->delivered_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <h6>Dirección de Envío</h6>
                    @if($order->address)
                        <address>
                            <p class="mb-1">{{ $order->address->first_name }} {{ $order->address->last_name }}</p>
                            <p class="mb-1">{{ $order->address->address_line_1 }}</p>
                            
                            @if($order->address->address_line_2)
                                <p class="mb-1">{{ $order->address->address_line_2 }}</p>
                            @endif
                            
                            <p class="mb-1">{{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->zip_code }}</p>
                            <p class="mb-1">{{ $order->address->country }}</p>
                            
                            @if($order->address->phone)
                                <p class="mb-1">Tel: {{ $order->address->phone }}</p>
                            @endif
                        </address>
                    @else
                        <p>No hay dirección de envío registrada para este pedido.</p>
                    @endif
                </div>
            </div>
            
            <h6>Productos Comprados</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
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
                                    <div class="d-flex align-items-center">
                                        @if($item->product && $item->product->thumbnail)
                                            <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->product_name }}" class="img-thumbnail mr-3" style="width: 60px;">
                                        @endif
                                        
                                        <div>
                                            <div>{{ $item->product_name }}</div>
                                            
                                            @if($item->options)
                                                <div class="small text-muted">
                                                    @foreach(json_decode($item->options, true) ?? [] as $key => $value)
                                                        <span class="badge badge-light">{{ $key }}: {{ $value }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            @if($item->product && $order->status->slug === 'delivered')
                                                <a href="{{ route('shop.products.show', $item->product) }}#review" class="small">Escribir reseña</a>
                                            @endif
                                        </div>
                                    </div>
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
        </div>
        
        <div class="card-footer bg-white">
            <div class="row">
                <div class="col-md-6">
                    @if($order->status->slug === 'pending' || $order->status->slug === 'processing')
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelOrderModal">
                            <i class="fas fa-ban"></i> Cancelar Pedido
                        </button>
                    @endif
                    
                    @if($order->status->slug === 'delivered' && $order->delivered_at && $order->delivered_at->diffInDays(now()) <= 30)
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#returnOrderModal">
                            <i class="fas fa-undo"></i> Solicitar Devolución
                        </button>
                    @endif
                </div>
                
                <div class="col-md-6 text-md-right mt-3 mt-md-0">
                    <a href="{{ route('shop.orders.download-invoice', $order) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-file-invoice"></i> Descargar Factura
                    </a>
                    
                    <form action="{{ route('shop.orders.reorder', $order) }}" method="POST" class="d-inline-block">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> Volver a Comprar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @if($order->shipment)
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Información de Envío</h5>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Estado:</strong> {{ $order->shipment->status }}</p>
                        <p class="mb-1"><strong>Compañía:</strong> {{ $order->shipment->shipping_company }}</p>
                        
                        @if($order->shipment->tracking_number)
                            <p class="mb-1">
                                <strong>Número de seguimiento:</strong> 
                                {{ $order->shipment->tracking_number }}
                            </p>
                        @endif
                        
                        @if($order->shipment->shipped_at)
                            <p class="mb-1">
                                <strong>Enviado el:</strong> 
                                {{ $order->shipment->shipped_at->format('d/m/Y H:i') }}
                            </p>
                        @endif
                        
                        @if($order->shipment->delivered_at)
                            <p class="mb-1">
                                <strong>Entregado el:</strong> 
                                {{ $order->shipment->delivered_at->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                    
                    <div class="col-md-6 text-md-right">
                        <a href="{{ route('shop.orders.track', $order) }}" class="btn btn-info">
                            <i class="fas fa-truck"></i> Seguir Envío
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Modal: Cancelar Pedido -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" role="dialog" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('shop.orders.cancel', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelOrderModalLabel">Cancelar Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas cancelar este pedido?</p>
                        
                        <div class="form-group">
                            <label for="reason">Motivo de la cancelación</label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger">Confirmar Cancelación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal: Solicitar Devolución -->
    <div class="modal fade" id="returnOrderModal" tabindex="-1" role="dialog" aria-labelledby="returnOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('shop.orders.request-return', $order) }}" method="POST">
                    @csrf
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="returnOrderModalLabel">Solicitar Devolución</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <p>Selecciona los productos que deseas devolver:</p>
                        
                        <div class="form-group">
                            @foreach($order->items as $item)
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" name="items[]" value="{{ $item->id }}" id="item-{{ $item->id }}">
                                    <label class="custom-control-label" for="item-{{ $item->id }}">
                                        {{ $item->product_name }} ({{ $item->quantity }} x {{ $order->currency }} {{ number_format($item->price, 2) }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="form-group">
                            <label for="return-reason">Motivo de la devolución</label>
                            <textarea name="reason" id="return-reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection