@php
    // Este componente genera datos estructurados JSON-LD para un producto
    $product = $product ?? null;
    
    if (!$product) return;
    
    // Recolectar datos del producto
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => $product->description ?? $product->short_description ?? '',
        'sku' => $product->sku ?? '',
        'mpn' => $product->sku ?? '',
        'brand' => [
            '@type' => 'Brand',
            'name' => $product->brand->name ?? ''
        ],
        'image' => [],
        'offers' => [
            '@type' => 'Offer',
            'url' => route('shop.products.show', $product->slug),
            'priceCurrency' => 'CLP', // Ajustar según la moneda de la tienda
            'price' => $product->special_price ?? $product->price,
            'availability' => $product->quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'seller' => [
                '@type' => 'Organization',
                'name' => config('app.name')
            ]
        ]
    ];
    
    // Agregar imágenes
    if (isset($product->images) && $product->images->count() > 0) {
        foreach ($product->images as $image) {
            $data['image'][] = url(Storage::url($image->image));
        }
    } elseif (isset($product->image)) {
        $data['image'][] = url(Storage::url($product->image));
    }
    
    // Agregar valoraciones si existen
    if (isset($product->ratings) && $product->ratings->count() > 0) {
        $avgRating = $product->ratings->avg('rating');
        $reviewCount = $product->ratings->count();
        
        $data['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => number_format($avgRating, 1),
            'reviewCount' => $reviewCount
        ];
    }
    
    $jsonLD = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
@endphp

<script type="application/ld+json">
{!! $jsonLD !!}
</script>