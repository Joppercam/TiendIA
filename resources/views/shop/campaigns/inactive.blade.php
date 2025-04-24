@extends('layouts.shop')

@section('content')
<div class="container py-5">
    <div class="text-center">
        <h1 class="mb-4">{{ $campaign->name }}</h1>
        
        <div class="alert alert-warning">
            <p class="mb-0">
                Esta campaña promocional no está activa actualmente.
                @if($campaign->starts_at->isFuture())
                    <br>
                    Comenzará el {{ $campaign->starts_at->format('d/m/Y') }}.
                @elseif($campaign->ends_at->isPast())
                    <br>
                    Finalizó el {{ $campaign->ends_at->format('d/m/Y') }}.
                @endif
            </p>
        </div>
        
        <p class="lead mt-4">{{ $campaign->description }}</p>
        
        <div class="mt-5">
            <a href="{{ route('shop.campaigns.index') }}" class="btn btn-outline-primary me-2">
                Ver otras campañas
            </a>
            <a href="{{ route('shop.products.index') }}" class="btn btn-primary">
                Explorar productos
            </a>
        </div>
    </div>
</div>
@endsection