<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TiendIA') }} - @yield('title', 'Tienda en línea')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div>
                        <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600">TiendIA</a>
                    </div>

                    <!-- Search -->
                    <div class="w-1/3">
                        <form action="{{ route('shop.products.index') }}" method="GET">
                            <input type="text" name="q" placeholder="Buscar productos..." class="w-full px-4 py-2 border rounded-lg">
                        </form>
                    </div>

                    <!-- Navigation -->
                    <nav>
                        <ul class="flex space-x-6">
                            <li>
                                <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600">Inicio</a>
                            </li>
                            <li>
                                <a href="{{ route('shop.products.index') }}" class="text-gray-700 hover:text-indigo-600">Productos</a>
                            </li>
                            <li>
                                <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-indigo-600">
                                    <span>Carrito</span>
                                    <span class="bg-indigo-600 text-white rounded-full px-2 py-1 text-xs">
                                        {{ session()->has('cart') ? count(session('cart')) : 0 }}
                                    </span>
                                </a>
                            </li>

                            @auth
                                <li>
                                    <div x-data="{ isOpen: false }">
                                        <button @click="isOpen = !isOpen" class="text-gray-700 hover:text-indigo-600">
                                            {{ auth()->user()->name }}
                                        </button>

                                        <div x-show="isOpen" @click.away="isOpen = false" class="absolute mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Perfil</a>
                                            <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Mis Pedidos</a>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Cerrar sesión</button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @else
                                <li>
                                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600">Iniciar sesión</a>
                                </li>
                                <li>
                                    <a href="{{ route('register') }}" class="text-gray-700 hover:text-indigo-600">Registrarse</a>
                                </li>
                            @endauth
                        </ul>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Categories menu -->
        <div class="bg-gray-800 text-white">
            <div class="container mx-auto px-4 py-2">
                <div class="flex items-center space-x-6">
                    @foreach(App\Models\Category::where('parent_id', null)->where('is_active', true)->take(7)->get() as $category)
                        <a href="{{ route('shop.products.category', $category->slug) }}" class="hover:text-indigo-300">{{ $category->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main content -->
        <main class="flex-grow">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">TiendIA</h3>
                        <p>Tu tienda online de confianza para todas tus necesidades.</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Enlaces</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('home') }}" class="hover:text-indigo-300">Inicio</a></li>
                            <li><a href="{{ route('shop.products.index') }}" class="hover:text-indigo-300">Productos</a></li>
                            <li><a href="#" class="hover:text-indigo-300">Sobre nosotros</a></li>
                            <li><a href="#" class="hover:text-indigo-300">Contacto</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Categorías</h3>
                        <ul class="space-y-2">
                            @foreach(App\Models\Category::where('parent_id', null)->where('is_active', true)->take(5)->get() as $category)
                                <li><a href="{{ route('shop.products.category', $category->slug) }}" class="hover:text-indigo-300">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                        <p>Email: info@tiendia.com</p>
                        <p>Teléfono: +1 234 567 890</p>
                        <div class="mt-4 flex space-x-4">
                            <a href="#" class="hover:text-indigo-300">Facebook</a>
                            <a href="#" class="hover:text-indigo-300">Twitter</a>
                            <a href="#" class="hover:text-indigo-300">Instagram</a>
                        </div>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-700 pt-6">
                    <p class="text-center">&copy; {{ date('Y') }} TiendIA. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </div>

    @yield('scripts')
</body>
</html>