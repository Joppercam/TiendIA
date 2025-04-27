@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Detalles de la Campaña: {{ $campaign->name }}</h4>
                    <div class="float-right">
                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-primary">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Información General</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Nombre</th>
                                    <td>{{ $campaign->name }}</td>
                                </tr>
                                <tr>
                                    <th>Slug</th>
                                    <td>{{ $campaign->slug }}</td>
                                </tr>
                                <tr>
                                    <th>Descripción</th>
                                    <td>{{ $campaign->description ?? 'Sin descripción' }}</td>
                                </tr>
                                <tr>
                                    <th>Estado</th>
                                    <td>
                                        @if($campaign->isActive())
                                            <span class="badge badge-success">Activa</span>
                                        @elseif($campaign->starts_at->isFuture())
                                            <span class="badge badge-info">Programada</span>
                                        @elseif($campaign->ends_at->isPast())
                                            <span class="badge badge-danger">Finalizada</span>
                                        @else
                                            <span class="badge badge-warning">Inactiva</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Fecha de Inicio</th>
                                    <td>{{ $campaign->starts_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha de Finalización</th>
                                    <td>{{ $campaign->ends_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Enlace Público</th>
                                    <td>
                                        <a href="{{ route('shop.campaigns.show', $campaign->slug) }}" target="_blank">
                                            {{ route('shop.campaigns.show', $campaign->slug) }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <h5>Banner</h5>
                            @if($campaign->banner_image)
                                <img src="{{ asset('storage/'.$campaign->banner_image) }}" alt="{{ $campaign->name }}" class="img-fluid">
                                <p class="mt-2">
                                    <strong>Enlace:</strong> 
                                    @if($campaign->banner_link)
                                        <a href="{{ $campaign->banner_link }}" target="_blank">{{ $campaign->banner_link }}</a>
                                    @else
                                        <span class="text-muted">Sin enlace</span>
                                    @endif
                                </p>
                            @else
                                <div class="text-center py-5 bg-light">
                                    <p class="text-muted">No hay banner disponible</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Promociones Asociadas</h5>
                        
                        <div class="mb-3">
                            <a href="{{ route('admin.promotions.create', ['campaign_id' => $campaign->id]) }}" class="btn btn-sm btn-success">
                                <i class="fa fa-plus"></i> Añadir Promoción
                            </a>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($promotions as $promotion)
                                        <tr>
                                            <td>{{ $promotion->name }}</td>
                                            <td>
                                                @switch($promotion->promotion_type)
                                                    @case('featured_product')
                                                        Producto Destacado
                                                        @break
                                                    @case('bundle')
                                                        Paquete Promocional
                                                        @break
                                                    @case('buy_x_get_y')
                                                        Compra X Lleva Y
                                                        @break
                                                    @case('discount')
                                                        Descuento Especial
                                                        @break
                                                    @default
                                                        {{ $promotion->promotion_type }}
                                                @endswitch
                                            </td>
                                            <td>{{ $promotion->starts_at ? $promotion->starts_at->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $promotion->ends_at ? $promotion->ends_at->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                @if($promotion->isValid())
                                                    <span class="badge badge-success">Activa</span>
                                                @else
                                                    <span class="badge badge-danger">Inactiva</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.promotions.edit', $promotion) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.promotions.destroy', $promotion) }}" method="POST" class="d-inline">
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
                                            <td colspan="6" class="text-center">No hay promociones asociadas a esta campaña</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $promotions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection