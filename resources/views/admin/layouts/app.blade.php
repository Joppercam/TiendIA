<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TiendIA') }} - Panel de Administración</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <div class="w-64 bg-gray-800 text-white fixed h-full overflow-y-auto">
            <div class="p-4">
                <h1 class="text-xl font-bold">TiendIA Admin</h1>
            </div>
            <nav class="mt-5">
                <x-admin.sidebar-link
                    href="{{ route('admin.dashboard') }}" {{-- Adjusted route name based on web.php --}}
                    icon="home"
                    :active="request()->routeIs('admin.dashboard')"
                >
                    Dashboard
                </x-admin.sidebar-link>

                <div class="px-4 py-2 text-xs text-gray-400 uppercase mt-2">
                    Catálogo
                </div>

                <x-admin.sidebar-link
                    href="{{ route('admin.products.index') }}"
                    icon="shopping-bag" {{-- Keep icon --}}
                    :active="request()->routeIs('admin.products.*')"
                >
                    Productos
                </x-admin.sidebar-link>

                <x-admin.sidebar-link
                    href="{{ route('admin.categories.index') }}"
                    icon="tag" {{-- Keep icon --}}
                    :active="request()->routeIs('admin.categories.*')"
                >
                    Categorías
                </x-admin.sidebar-link>

                <x-admin.sidebar-link
                    href="{{ route('admin.brands.index') }}"
                    icon="bookmark" {{-- New icon suggestion --}}
                    :active="request()->routeIs('admin.brands.*')"
                >
                    Marcas
                </x-admin.sidebar-link>

                 <div class="px-4 py-2 text-xs text-gray-400 uppercase mt-2">
                    Ventas
                </div>

                <x-admin.sidebar-link
                    href="{{ route('admin.orders.index') }}"
                    icon="shopping-cart" {{-- Keep icon --}}
                    :active="request()->routeIs('admin.orders.*')"
                >
                    Pedidos
                </x-admin.sidebar-link>

                 <x-admin.sidebar-link
                    href="{{ route('admin.payments.index') }}"
                    icon="credit-card" {{-- New icon suggestion --}}
                    :active="request()->routeIs('admin.payments.*')"
                >
                    Historial Pagos
                </x-admin.sidebar-link>

                <div class="px-4 py-2 text-xs text-gray-400 uppercase mt-2">
                    Inventario
                </div>

                 <x-admin.sidebar-link
                    href="{{ route('admin.inventory.dashboard') }}" {{-- Link to inventory dashboard --}}
                    icon="archive-box" {{-- New icon suggestion --}}
                    :active="request()->routeIs('admin.inventory.dashboard') || request()->routeIs('admin.inventory-items.*') || request()->routeIs('admin.inventory-movements.*')"
                >
                    Gestión Inventario
                </x-admin.sidebar-link>

                <x-admin.sidebar-link
                    href="{{ route('admin.suppliers.index') }}"
                    icon="truck" {{-- New icon suggestion --}}
                    :active="request()->routeIs('admin.suppliers.*')"
                >
                    Proveedores
                </x-admin.sidebar-link>

                <div class="px-4 py-2 text-xs text-gray-400 uppercase mt-2">
                    Marketing
                </div>

                 <x-admin.sidebar-link
                    href="{{ route('admin.coupons.index') }}"
                    icon="ticket" {{-- New icon suggestion --}}
                    :active="request()->routeIs('admin.coupons.*')"
                >
                    Cupones
                </x-admin.sidebar-link>

                 <x-admin.sidebar-link
                    href="{{ route('admin.promotions.index') }}"
                    icon="gift" {{-- New icon suggestion --}}
                    :active="request()->routeIs('admin.promotions.*')"
                >
                    Promociones
                </x-admin.sidebar-link>

                 <x-admin.sidebar-link
                    href="{{ route('admin.discounts.index') }}"
                    icon="receipt-percent" {{-- New icon suggestion --}}
                    :active="request()->routeIs('admin.discounts.*')"
                >
                    Descuentos
                </x-admin.sidebar-link>

                <x-admin.sidebar-link
                    href="{{ route('admin.campaigns.index') }}"
                    icon="megaphone" {{-- New icon suggestion --}}
                    :active="request()->routeIs('admin.campaigns.*')"
                >
                    Campañas
                </x-admin.sidebar-link>


                <div class="px-4 py-2 text-xs text-gray-400 uppercase mt-2">
                    Usuarios y Contenido
                </div>

                <x-admin.sidebar-link
                    href="{{ route('admin.users.index') }}"
                    icon="users" {{-- Keep icon --}}
                    :active="request()->routeIs('admin.users.*')"
                >
                    Usuarios
                </x-admin.sidebar-link>

                <x-admin.sidebar-link
                    href="{{ route('admin.reviews.dashboard') }}" {{-- Link to reviews dashboard --}}
                    icon="star" {{-- New icon suggestion --}}
                    :active="request()->routeIs('admin.reviews.*')"
                >
                    Reseñas y Preguntas
                </x-admin.sidebar-link>


                <div class="px-4 py-2 text-xs text-gray-400 uppercase mt-2">
                    Reportes
                </div>

                <x-admin.sidebar-link
                    href="{{ route('admin.reports.sales') }}"
                    icon="chart-bar" {{-- Keep icon --}}
                    :active="request()->routeIs('admin.reports.sales')"
                >
                    Ventas
                </x-admin.sidebar-link>

                <x-admin.sidebar-link
                    href="{{ route('admin.reports.products') }}"
                    icon="chart-pie" {{-- Keep icon --}}
                    :active="request()->routeIs('admin.reports.products')"
                >
                    Productos
                </x-admin.sidebar-link>

                <x-admin.sidebar-link
                    href="{{ route('admin.reports.customers') }}"
                    icon="user-group" {{-- Keep icon --}}
                    :active="request()->routeIs('admin.reports.customers')"
                >
                    Clientes
                </x-admin.sidebar-link>
                {{-- You might have a general reports index:
                 <x-admin.sidebar-link
                    href="{{ route('admin.reports.index') }}"
                    icon="document-chart-bar"
                    :active="request()->routeIs('admin.reports.index')"
                >
                    General Reportes
                </x-admin.sidebar-link>
                --}}


                <div class="px-4 py-2 text-xs text-gray-400 uppercase mt-2">
                    Configuración
                </div>

                <x-admin.sidebar-link
                    href="{{ route('admin.payment-gateways.index') }}"
                    icon="cog" {{-- New icon suggestion --}}
                    :active="request()->routeIs('admin.payment-gateways.*')"
                >
                    Pasarelas de Pago
                </x-admin.sidebar-link>

                <x-admin.sidebar-link
                    href="{{ route('admin.seo.dashboard') }}" {{-- Link to SEO dashboard --}}
                    icon="magnifying-glass" {{-- New icon suggestion --}}
                    :active="request()->routeIs('admin.seo.*')"
                >
                    SEO
                </x-admin.sidebar-link>

                {{-- Add other settings links here if needed --}}

            </nav>
        </div>

        <div class="flex-1 ml-64">
            <div class="bg-white shadow-sm z-10 relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16">
                    <div class="flex items-center">
                        <h2 class="text-xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h2>
                    </div>

                    <div class="flex items-center">
                        <div class="mr-4">
                            <input type="text" placeholder="Buscar..." class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                        </div>

                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Abrir menú de usuario</span>
                                    {{-- Use Auth::user()->profile_photo_url ?? default image --}}
                                    <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->profile_photo_url ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80' }}" alt="{{ Auth::user()->name ?? 'Usuario' }}">
                                </button>
                            </div>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                 role="menu"
                                 aria-orientation="vertical"
                                 aria-labelledby="user-menu-button"
                                 tabindex="-1"
                                 style="display: none;"> {{-- Add style="display: none;" for Alpine --}}
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-0">Tu Perfil</a>
                                {{-- Add link to general settings if you have one --}}
                                {{-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-1">Configuración Tienda</a> --}}
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-2">Cerrar Sesión</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    {{-- Flash messages for Livewire --}}
                     @if (session()->has('message'))
                        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded" role="alert">
                            <p>{{ session('message') }}</p>
                        </div>
                    @endif

                    @yield('content')
                     {{-- If using Livewire components directly in content --}}
                    {{-- {{ $slot ?? '' }} --}}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>