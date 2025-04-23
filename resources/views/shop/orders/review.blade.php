@extends('layouts.app')

@section('title', 'Escribir Reseñas - Pedido #' . $order->order_number)

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('shop.orders.show', $order) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Pedido
        </a>
    </div>
    
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Escribir Reseñas - Pedido #{{ $order->order_number }}</h5>
        </div>
        
        <div class="card-body">
            @if($pendingReviews->count() > 0)
                <p>Comparte tu opinión sobre los productos que has comprado. Tu experiencia ayudará a otros compradores.</p>
                
                <div class="row">
                    @foreach($pendingReviews as $item)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex mb-3">
                                        @if($item->product && $item->product->thumbnail)
                                            <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->product_name }}" class="img-thumbnail mr-3" style="width: 80px;">
                                        @endif
                                        
                                        <div>
                                            <h6>{{ $item->product_name }}</h6>
                                            <p class="text-muted small">
                                                {{ $order->currency }} {{ number_format($item->price, 2) }} x {{ $item->quantity }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <form action="{{ route('shop.orders.store-review', $order) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        
                                        <div class="form-group">
                                            <label>Puntuación</label>
                                            <div class="rating-input">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rating" id="rating-{{ $item->id }}-5" value="5" required>
                                                    <label class="form-check-label" for="rating-{{ $item->id }}-5">5 ★</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rating" id="rating-{{ $item->id }}-4" value="4">
                                                    <label class="form-check-label" for="rating-{{ $item->id }}-4">4 ★</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rating" id="rating-{{ $item->id }}-3" value="3">
                                                    <label class="form-check-label" for="rating-{{ $item->id }}-3">3 ★</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rating" id="rating-{{ $item->id }}-2" value="2">
                                                    <label class="form-check-label" for="rating-{{ $item->id }}-2">2 ★</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rating" id="rating-{{ $item->id }}-1" value="1">
                                                    <label class="form-check-label" for="rating-{{ $item->id }}-1">1 ★</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="title-{{ $item->id }}">Título</label>
                                            <input type="text" class="form-control" id="title-{{ $item->id }}" name="title" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="comment-{{ $item->id }}">Comentario</label>
                                            <textarea class="form-control" id="comment-{{ $item->id }}" name="comment" rows="3" required></textarea>
                                            <small class="form-text text-muted">Mínimo 10 caracteres</small>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Enviar Reseña</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>¡Ya has dejado reseñas para todos los productos de este pedido!</h5>
                    <p class="text-muted">Gracias por compartir tu opinión con nosotros.</p>
                    <a href="{{ route('shop.orders.show', $order) }}" class="btn btn-primary mt-2">
                        Volver al Pedido
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection