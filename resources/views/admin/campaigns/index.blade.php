@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Gestión de Campañas</h4>
                    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary float-right">
                        <i class="fa fa-plus"></i> Crear Campaña
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
                                    <th>Nombre</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Estado</th>
                                    <th>Banner</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                    <tr>
                                        <td>{{ $campaign->name }}</td>
                                        <td>{{ $campaign->starts_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $campaign->ends_at->format('d/m/Y H:i') }}</td>
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
                                        <td>
                                            @if($campaign->banner_image)
                                                <img src="{{ asset('storage/'.$campaign->banner_image) }}" alt="{{ $campaign->name }}" class="img-thumbnail" style="max-height: 50px;">
                                            @else
                                                <span class="text-muted">Sin banner</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.campaigns.show', $campaign) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" class="d-inline">
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
                                        <td colspan="6" class="text-center">No hay campañas registradas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $campaigns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection