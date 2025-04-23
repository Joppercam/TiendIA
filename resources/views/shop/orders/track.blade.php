@extends('layouts.app')

@section('title', 'Seguimiento de Pedido #' . $order->order_number)

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('shop.orders.show', $order) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Pedido
        </a>
    </div>
    
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Seguimiento del Pedido #{{ $order->order_number }}</h5>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Información del Envío</h6>
                    <p class="mb-1"><strong>Compañía:</strong> {{ $order->shipment->shipping_company }}</p>
                    
                    @if($order->shipment->tracking_number)
                        <p class="mb-1">
                            <strong>Número de seguimiento:</strong> 
                            {{ $order->shipment->tracking_number }}
                        </p>
                    @endif
                    
                    <p class="mb-1"><strong>Estado actual:</strong> {{ $order->shipment->status }}</p>
                </div>
                
                <div class="col-md-6">
                    <h6>Dirección de Entrega</h6>
                    @if($order->address)
                        <address>
                            <p class="mb-1">{{ $order->address->first_name }} {{ $order->address->last_name }}</p>
                            <p class="mb-1">{{ $order->address->address_line_1 }}</p>
                            
                            @if($order->address->address_line_2)
                                <p class="mb-1">{{ $order->address->address_line_2 }}</p>
                            @endif
                            
                            <p class="mb-1">{{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->zip_code }}</p>
                            <p class="mb-1">{{ $order->address->country }}</p>
                        </address>
                    @else
                        <p>No hay dirección de envío registrada para este pedido.</p>
                    @endif
                </div>
            </div>
            
            <h6>Estado del Envío</h6>
            
            <div class="tracking-timeline mt-4">
                @if($order->shipment->tracking_details)
                    <ul class="list-group">
                        @foreach($order->shipment->tracking_details as $detail)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $detail['status'] }}</strong>
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($detail['date'])->format('d/m/Y H:i') }}</span>
                                </div>
                                
                                @if(isset($detail['location']) && $detail['location'])
                                    <div class="text-muted">{{ $detail['location'] }}</div>
                                @endif
                                
                                @if(isset($detail['description']) && $detail['description'])
                                    <div>{{ $detail['description'] }}</div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="timeline">
                        <div class="timeline-item {{ $order->status->slug == 'processing' || $order->status->slug == 'shipped' || $order->status->slug == 'delivered' ? 'active' : '' }}">
                            <div class="timeline-circle"></div>
                            <div class="timeline-content">
                                <h6>Procesando Pedido</h6>
                                <p class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $order->status->slug == 'shipped' || $order->status->slug == 'delivered' ? 'active' : '' }}">
                            <div class="timeline-circle"></div>
                            <div class="timeline-content">
                                <h6>Pedido Enviado</h6>
                                <p class="text-muted">
                                    {{ $order->shipped_at ? $order->shipped_at->format('d/m/Y H:i') : 'Pendiente' }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $order->status->slug == 'delivered' ? 'active' : '' }}">
                            <div class="timeline-circle"></div>
                            <div class="timeline-content">
                                <h6>Pedido Entregado</h6>
                                <p class="text-muted">
                                    {{ $order->delivered_at ? $order->delivered_at->format('d/m/Y H:i') : 'Pendiente' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card-footer bg-white">
            @if($order->shipment->tracking_number && $order->shipment->shipping_company)
                <p class="mb-2">También puedes seguir tu envío directamente en el sitio web de la compañía de transporte:</p>
                
                @if($order->shipment->shipping_company == 'DHL')
                    <a href="https://www.dhl.com/es-es/home/tracking/tracking-express.html?submit=1&tracking-id={{ $order->shipment->tracking_number }}" target="_blank" class="btn btn-outline-primary">
                        Seguir en DHL
                    </a>
                @elseif($order->shipment->shipping_company == 'FedEx')
                    <a href="https://www.fedex.com/es-es/tracking.html?tracknumbers={{ $order->shipment->tracking_number }}" target="_blank" class="btn btn-outline-primary">
                        Seguir en FedEx
                    </a>
                @elseif($order->shipment->shipping_company == 'UPS')
                    <a href="https://www.ups.com/track?track=yes&trackNums={{ $order->shipment->tracking_number }}" target="_blank" class="btn btn-outline-primary">
                        Seguir en UPS
                    </a>
                @elseif($order->shipment->shipping_company == 'Correos')
                    <a href="https://www.correos.es/es/es/herramientas/localizador/envios?number={{ $order->shipment->tracking_number }}" target="_blank" class="btn btn-outline-primary">
                        Seguir en Correos
                    </a>
                @else
                    <p class="text-muted">El seguimiento directo no está disponible para esta compañía de envío.</p>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
.tracking-timeline {
    padding: 0 15px;
}

.timeline {
    position: relative;
    padding: 30px 0;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    left: 15px;
    height: 100%;
    width: 2px;
    background: #e5e5e5;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 30px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-circle {
    position: absolute;
    top: 0;
    left: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e5e5e5;
    border: 5px solid #fff;
    z-index: 1;
}

.timeline-item.active .timeline-circle {
    background: #4CAF50;
}

.timeline-content {
    padding-top: 3px;
}
</style>
@endsection