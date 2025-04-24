@extends('layouts.shop')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Campañas y Promociones</h1>
    
    <div class="row">
        @forelse($campaigns as $campaign)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    @if($campaign->banner_image)
                        <img src="{{ asset('storage/'.$campaign->banner_image) }}" class="card-img-top" alt="{{ $campaign->name }}">
                    @else
                        <div class="bg-light text-center py-5">
                            <i class="fa fa-tag fa-3x text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $campaign->name }}</h5>
                        <p class="card-text">{{ Str::limit($campaign->description, 100) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Válido hasta: {{ $campaign->ends_at->format('d/m/Y') }}
                                </small>
                            </div>
                            <a href="{{ route('shop.campaigns.show', $campaign->slug) }}" class="btn btn-primary">Ver Ofertas</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <p class="mb-0">No hay campañas activas en este momento. ¡Vuelve pronto para ver nuestras promociones!</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection