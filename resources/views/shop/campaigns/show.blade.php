@extends('layouts.shop')

@section('content')
<div class="container py-5">
    @if($campaign->banner_image)
        <div class="campaign-banner mb-4">
            @if($campaign->banner_link)
                <a href="{{ $campaign->banner_link }}" target="_blank">
                    <img src="{{ asset('storage/'.$campaign->banner_image) }}" class="img-fluid rounded" alt="{{ $campaign->name }}">
                </a>
            @else
                <img src="{{ asset('storage/'.$campaign->banner_image) }}" class="img-fluid rounded" alt="{{ $campaign->name }}">
            @endif
        </div>
    @endif
    
    <div class="campaign-header mb-4">
        <h1>{{ $campaign->name }}</h1>
        <p class="lead">{{ $campaign->description }}</p>
        
        <div class="campaign-dates">
            <span class="badge badge-primary">
                Válido hasta: {{ $campaign->ends_at->format('d/m/Y') }}
            </span>
        </div>
    </div>
    
    @if($featuredProducts->isNotEmpty())
        <div class="section mb-5">
            <h2 class="section-title mb-4">Productos Destacados</h2>
            <div class="row">
                @foreach($featuredProducts as $product)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 product-card">
                            <a href="{{ route('shop.products.show', $product->slug) }}">
                                @if($product->getFirstMediaUrl('images'))
                                    <img src="{{ $product->getFirstMediaUrl('images') }}" class="card-img-top" alt="{{ $product->name }}">
                                @else
                                    <div class="bg-light text-center py-4">
                                        <i class="fa fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                
                                <div class="product-price mb-2">
                                    @php
                                        $priceInfo = app(\App\Services\PromotionService::class)->calculateDiscountedPrice($product);
                                    @endphp
                                    
                                    @if($priceInfo['has_discount'])
                                        <span class="original-price text-muted text-decoration-line-through">
                                            {{ formatMoney($priceInfo['original_price']) }}
                                        </span>
                                        <span class="final-price text-danger">
                                            {{ formatMoney($priceInfo['final_price']) }}
                                        </span>
                                        // resources/views/shop/campaigns/show.blade.php (continuación)
                                        <span class="discount-badge bg-danger text-white px-2 py-1 rounded">
                                            -{{ $priceInfo['discount_percentage'] }}%
                                        </span>
                                    @else
                                        <span class="final-price">
                                            {{ formatMoney($product->price) }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="{{ route('shop.products.show', $product->slug) }}" class="btn btn-outline-primary btn-sm">Ver Detalles</a>
                                    <button class="btn btn-primary btn-sm add-to-cart" data-product-id="{{ $product->id }}">
                                        <i class="fa fa-shopping-cart"></i> Añadir al Carrito
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    @if($featuredCategories->isNotEmpty())
        <div class="section mb-5">
            <h2 class="section-title mb-4">Categorías en Oferta</h2>
            <div class="row">
                @foreach($featuredCategories as $category)
                    <div class="col-md-3 mb-4">
                        <div class="card text-center h-100">
                            <a href="{{ route('shop.categories.show', $category->slug) }}" class="text-decoration-none">
                                @if($category->image)
                                    <img src="{{ asset('storage/'.$category->image) }}" class="card-img-top category-image" alt="{{ $category->name }}">
                                @else
                                    <div class="bg-light py-4">
                                        <i class="fa fa-folder fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $category->name }}</h5>
                                    <p class="card-text text-muted">{{ Str::limit($category->description, 80) }}</p>
                                    <span class="btn btn-outline-primary btn-sm">Ver productos</span>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <div class="campaign-footer text-center mt-5">
        <p class="text-muted">
            * Promociones válidas hasta {{ $campaign->ends_at->format('d/m/Y') }} o hasta agotar existencias.
            <br>Descuentos no acumulables con otras promociones.
        </p>
        
        <div class="mt-4">
            <a href="{{ route('shop.products.index') }}" class="btn btn-outline-primary">
                Explorar todo el catálogo
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Funcionalidad para añadir al carrito (implementar según sea necesario)
        const addToCartButtons = document.querySelectorAll('.add-to-cart');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                
                // Aquí iría la llamada AJAX para añadir al carrito
                // Este es un ejemplo básico que deberá adaptarse a la implementación del carrito
                fetch('{{ route("shop.cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar notificación de éxito
                        alert('Producto añadido al carrito');
                        // Actualizar contador del carrito si es necesario
                    } else {
                        alert(data.message || 'Error al añadir el producto');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ha ocurrido un error al añadir el producto al carrito');
                });
            });
        });
    });
</script>
@endpush