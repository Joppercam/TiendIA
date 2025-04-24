@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Gestión de Cupones</h4>
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary float-right">
                        <i class="fa fa-plus"></i> Crear Cupón
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Mín. Compra</th>
                                    <th>Límite Uso</th>
                                    <th>Usado</th>
                                    <th>Vigencia</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupons as $coupon)
                                    <tr>
                                        <td>{{ $coupon->code }}</td>
                                        <td>{{ $coupon->type === 'percentage' ? 'Porcentaje' : 'Monto Fijo' }}</td>
                                        <td>
                                            @if($coupon->type === 'percentage')
                                                {{ number_format($coupon->value, 0) }}%
                                            @else
                                                ${{ number_format($coupon->value, 2) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->min_purchase)
                                                ${{ number_format($coupon->min_purchase, 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $coupon->usage_limit ?: 'Ilimitado' }}</td>
                                        <td>{{ $coupon->usage_count }} veces</td>
                                        <td>
                                            @if($coupon->starts_at && $coupon->expires_at)
                                                Del {{ $coupon->starts_at->format('d/m/Y') }} al {{ $coupon->expires_at->format('d/m/Y') }}
                                            @elseif($coupon->expires_at)
                                                Hasta {{ $coupon->expires_at->format('d/m/Y') }}
                                            @elseif($coupon->starts_at)
                                                Desde {{ $coupon->starts_at->format('d/m/Y') }}
                                            @else
                                                Sin fecha
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->isValid())
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No hay cupones registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $coupons->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection