<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TiendIA') }} - Panel de Administración</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white fixed h-full">
            <div class="p-4">
                <h1 class="text-xl font-bold">TiendIA Admin</h1>
            </div>
            <nav class="mt-5">
                <x-admin.sidebar-link
                    href="{{ route('admin.dashboard') }}"
                    icon="home"
                    :active="request()->routeIs('admin.dashboard')"
                >
                    Dashboard
                </x-admin.sidebar-link>
                
                <div class="px-4 py-2 text-xs text-gray-400 uppercase">
                    Gestión
                </div>
                
                <x-admin.sidebar-link
                    href="{{ route('admin.products.index') }}"
                    icon="shopping-bag"
                    :active="request()->routeIs('admin.products.*')"
                >
                    Productos
                </x-admin.sidebar-link>
                
                <x-admin.sidebar-link
                    href="{{ route('admin.categories.index') }}"
                    icon="tag"
                    :active="request()->routeIs('admin.categories.*')"
                >
                    Categorías
                </x-admin.sidebar-link>
                
                <x-admin.sidebar-link
                    href="{{ route('admin.orders.index') }}"
                    icon="shopping-cart"
                    :active="request()->routeIs('admin.orders.*')"
                >
                    Pedidos
                </x-admin.sidebar-link>
                
                <x-admin.sidebar-link
                    href="{{ route('admin.users.index') }}"
                    icon="users"
                    :active="request()->routeIs('admin.users.*')"
                >
                    Usuarios
                </x-admin.sidebar-link>
                
                <div class="px-4 py-2 text-xs text-gray-400 uppercase mt-2">
                    Reportes
                </div>
                
                <x-admin.sidebar-link
                    href="{{ route('admin.reports.sales') }}"
                    icon="chart-bar"
                    :active="request()->routeIs('admin.reports.sales')"
                >
                    Ventas
                </x-admin.sidebar-link>
                
                <x-admin.sidebar-link
                    href="{{ route('admin.reports.products') }}"
                    icon="chart-pie"
                    :active="request()->routeIs('admin.reports.products')"
                >
                    Productos
                </x-admin.sidebar-link>
                
                <x-admin.sidebar-link
                    href="{{ route('admin.reports.customers') }}"
                    icon="user-group"
                    :active="request()->routeIs('admin.reports.customers')"
                >
                    Clientes
                </x-admin.sidebar-link>
                
                <div class="px-4 py-2 text-xs text-gray-400 uppercase mt-2">
                    Configuración
                </div>
                
                <x-admin.sidebar-link
                    href="{{ route('admin.settings.general') }}"
                    icon="cog"
                    :active="request()->routeIs('admin.settings.*')"
                >
                    Ajustes
                </x-admin.sidebar-link>
            </nav>
        </div>

        <!-- Content -->
        <div class="flex-1 ml-64">
            <!-- Top Navbar -->
            <div class="bg-white shadow-sm z-10 relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16">
                    <div class="flex items-center">
                        <h2 class="text-xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h2>
                    </div>
                    
                    <div class="flex items-center">
                        <!-- Search -->
                        <div class="mr-4">
                            <input type="text" placeholder="Buscar..." class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Abrir menú de usuario</span>
                                    <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                                </button>
                            </div>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                 role="menu" 
                                 aria-orientation="vertical" 
                                 aria-labelledby="user-menu-button" 
                                 tabindex="-1">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Tu Perfil</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Configuración</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Cerrar Sesión</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Page Content -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>