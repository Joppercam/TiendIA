@extends('admin.layouts.app')

@section('title', 'Gestión de Pedidos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Pedidos</h3>
                    <div class="card-tools">
                        <form action="{{ route('admin.orders.index') }}" method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="order_number" class="form-control" placeholder="Número de pedido" value="{{ request('order_number') }}">
                                <select name="status" class="form-control ml-2">
                                    <option value="">Todos los estados</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->slug }}" {{ request('status') == $status->slug ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Número</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>
                                        @if($order->user)
                                            {{ $order->user->name }}
                                        @else
                                            {{ $order->guest_name }} (Invitado)
                                        @endif
                                    </td>
                                    <td>{{ $order->currency }} {{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $order->status->color }}">
                                            {{ $order->status->name }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay pedidos disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection