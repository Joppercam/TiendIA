<div class="featured-products-component py-4">
    <h2 class="section-title mb-4">{{ $title }}</h2>
    
    @if($products->isEmpty())
        <div class="alert alert-info">
            <p class="mb-0">No hay productos destacados disponibles actualmente.</p>
        </div>
    @else
        <div class="row">
            @foreach($products as $product)
                <div class="col-6 col-md-3 mb-4">
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
                            
                            <x-product-price :product="$product" />
                            
                            <div class="d-grid gap-2 mt-3">
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
    @endif
</div>