@php
// Definimos los colores para usarlos fácilmente. Idealmente, estos estarían en tu tailwind.config.js
$charcoal = '#222222'; // O usa clases como bg-gray-800, bg-gray-900
$electricBlue = '#007BFF'; // O usa clases como bg-blue-600, text-blue-600
@endphp

{{-- Usamos el layout principal de la tienda --}}
@extends('layouts.shop')

@section('title', 'Tienda Tecnológica IA')

@section('content')

    <div class="bg-gray-900 text-white pt-16 pb-20"> {{-- Color Carbón --}}
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0 text-center md:text-left">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">TiendIA: Innovación y Tecnología a tu Alcance</h1>
                <p class="text-xl text-gray-300 mb-6">Descubre las últimas tendencias en IA, gadgets y electrónica de consumo.</p>
                {{-- Barra de búsqueda prominente --}}
                <form action="{{ route('shop.products.index') }}" method="GET" class="mb-6 max-w-md mx-auto md:mx-0">
                    <div class="relative">
                        <input type="text" name="q" placeholder="Buscar productos, marcas..." class="w-full px-4 py-3 border-0 rounded-lg text-gray-800 focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="absolute right-0 top-0 bottom-0 bg-blue-600 text-white px-4 rounded-r-lg hover:bg-blue-700"> {{-- Color Azul Eléctrico --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </form>
                <a href="#novedades" class="bg-blue-600 text-white font-semibold py-3 px-8 rounded-lg hover:bg-blue-700 transition-colors"> {{-- Color Azul Eléctrico --}}
                    Ver Novedades
                </a>
            </div>
            <div class="md:w-1/2 flex justify-center">
                {{-- ** Importante: Reemplaza esto con una imagen real o un componente de slider ** --}}
                <div class="bg-gray-700 w-full h-80 rounded-lg shadow-2xl flex items-center justify-center">
                    <span class="text-gray-400 text-lg">Imagen Hero (1200x600 aprox)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"> {{-- Color Azul Eléctrico --}}
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1M13 16l2 4h4l-2-4M13 16H4m13 0h4M4 11h7" />
                    </svg>
                    <h4 class="font-semibold">Envío Rápido</h4>
                    <p class="text-sm text-gray-600">Entregas veloces a todo Chile.</p>
                </div>
                <div class="flex flex-col items-center">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"> {{-- Color Azul Eléctrico --}}
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <h4 class="font-semibold">Pago Seguro</h4>
                    <p class="text-sm text-gray-600">Plataformas 100% confiables.</p>
                </div>
                <div class="flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"> {{-- Color Azul Eléctrico --}}
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <h4 class="font-semibold">Garantía Tech</h4>
                    <p class="text-sm text-gray-600">Productos con garantía asegurada.</p>
                </div>
                 <div class="flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"> {{-- Color Azul Eléctrico --}}
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h4 class="font-semibold">Soporte IA</h4>
                    <p class="text-sm text-gray-600">Asistencia inteligente para tus dudas.</p>
                </div>
            </div>
        </div>
    </div>

    @if($newProducts->count() > 0)
        <div id="novedades" class="container mx-auto px-4 py-16">
            <h2 class="text-3xl font-bold mb-8 text-center">Novedades Tecnológicas</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($newProducts as $product)
                    @include('partials._product_card', ['product' => $product]) {{-- Usamos un partial para la tarjeta --}}
                @endforeach
            </div>
        </div>
    @endif

    @if($saleProducts->count() > 0)
        <div class="bg-gray-100 py-16"> {{-- Fondo gris claro para destacar --}}
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold mb-8 text-center">Tecno-Ofertas Imperdibles</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($saleProducts as $product)
                         @include('partials._product_card', ['product' => $product, 'show_discount_badge' => true])
                    @endforeach
                </div>
                <div class="mt-8 text-center">
                    <a href="{{ route('shop.products.index', ['on_sale' => true]) }}" {{-- Asumiendo que puedes filtrar por oferta --}}
                       class="bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-blue-700 transition-colors"> {{-- Color Azul Eléctrico --}}
                        Ver Todas las Ofertas
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="container mx-auto px-4 py-16">
        <h2 class="text-3xl font-bold mb-8 text-center">Los Favoritos de la Comunidad</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- ** Placeholder: Aquí iría un loop con los productos más vendidos ** --}}
            {{-- Necesitas obtener $bestSellingProducts desde tu HomeController --}}
            @for ($i = 0; $i < 4; $i++)
                <div class="bg-white rounded-lg shadow-md overflow-hidden animate-pulse">
                    <div class="h-48 bg-gray-200"></div>
                    <div class="p-4">
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                        <div class="h-8 bg-gray-200 rounded w-1/3 mb-4"></div>
                        <div class="h-10 bg-gray-300 rounded"></div>
                    </div>
                </div>
            @endfor
        </div>
         <p class="text-center text-gray-500 mt-4 text-sm">
            (Sección 'Más Vendidos' pendiente de implementación en el controlador)
        </p>
    </div>

     @if($categories->count() > 0)
        <div class="bg-gray-100 py-16">
             <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold mb-8 text-center">Explora Nuestras Categorías</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
                    {{-- Mostramos un máximo de 6 categorías principales --}}
                    @foreach($categories->take(6) as $category)
                        <a href="{{ route('shop.products.category', $category->slug) }}" class="group block relative rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow aspect-w-3 aspect-h-2">
                             @if($category->image)
                                <img src="{{ asset('storage/categories/' . $category->image) }}" alt="{{ $category->name }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                             @else
                                <div class="absolute inset-0 w-full h-full bg-gray-300 flex items-center justify-center">
                                     <span class="text-gray-500 text-xl font-semibold">{{ $category->name }}</span>
                                </div>
                             @endif
                             <div class="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-50 transition-opacity flex items-end p-4">
                                <h3 class="text-white text-xl font-bold">{{ $category->name }}</h3>
                             </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="bg-gray-900 text-gray-200 py-16"> {{-- Color Carbón --}}
        <div class="container mx-auto px-4 text-center">
             <h2 class="text-3xl font-bold mb-4">Mantente Conectado</h2>
             <p class="text-gray-400 mb-6 max-w-xl mx-auto">Recibe en tu correo las últimas novedades tecnológicas, ofertas exclusivas y consejos de IA.</p>
             <form action="#" method="POST" class="max-w-md mx-auto">
                 @csrf
                 <div class="flex">
                    <input type="email" name="email" placeholder="Tu correo electrónico" required class="flex-grow px-4 py-2 border-0 rounded-l-lg text-gray-800 focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-r-lg hover:bg-blue-700 transition-colors"> {{-- Color Azul Eléctrico --}}
                        Suscribirme
                    </button>
                 </div>
             </form>
        </div>
    </div>

@endsection

@push('scripts')
{{-- Si usas un carrusel/slider (como Swiper.js o Slick Carousel), necesitarás incluir su JS aquí --}}
{{-- <script src="path/to/your/carousel/library.js"></script>
<script>
  // Inicializa tus carruseles aquí
</script> --}}
@endpush