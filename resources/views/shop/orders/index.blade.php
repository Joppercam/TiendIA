@extends('layouts.app')

@section('title', 'Mis Pedidos')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Mis Pedidos</h1>
    
    <div class="card">
        <div class="card-header bg-white">
            <form action="{{ route('shop.orders.index') }}" method="GET" class="d-md-flex justify-content-between">
                <div class="input-group mb-2 mb-md-0" style="max-width: 300px;">
                    <input type="text" name="order_number" class="form-control" placeholder="Buscar por número" value="{{ request('order_number') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <div class="d-flex">
                    <select name="status" class="form-control mr-2">
                        <option value="">Todos los estados</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En proceso</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Enviado</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregado</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </form>
        </div>
        
        <div class="card-body p-0">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Pedido</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $order->status->color }}; color: white;">
                                            {{ $order->status->name }}
                                        </span>
                                    </td>
                                    <td>{{ $order->currency }} {{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <a href="{{ route('shop.orders.show', $order) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        
                                        @if($order->shipment)
                                            <a href="{{ route('shop.orders.track', $order) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-truck"></i> Seguir
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h5>No has realizado ningún pedido aún</h5>
                    <p class="text-muted">¡Empieza a comprar ahora!</p>
                    <a href="{{ route('shop.products.index') }}" class="btn btn-primary">
                        Ir a la tienda
                    </a>
                </div>
            @endif
        </div>
        
        @if($orders->hasPages())
            <div class="card-footer bg-white">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection