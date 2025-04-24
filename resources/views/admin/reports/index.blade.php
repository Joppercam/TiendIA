@extends('admin.layouts.app')

@section('header', 'Reportes y Estadísticas')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Tarjeta de Reporte de Ventas -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-indigo-600">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-white text-indigo-600 mr-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Ventas</h3>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Analiza las ventas por períodos, obtén información sobre tendencias y evalúa el rendimiento financiero de tu tienda.</p>
                <a href="{{ route('admin.reports.sales') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Ver Reporte
                </a>
            </div>
        </div>
        
        <!-- Tarjeta de Reporte de Productos -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-green-600">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-white text-green-600 mr-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Productos</h3>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Descubre qué productos son más populares, cuáles generan más ingresos y cuáles tienen bajo stock.</p>
                <a href="{{ route('admin.reports.products') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Ver Reporte
                </a>
            </div>
        </div>
        
        <!-- Tarjeta de Reporte de Clientes -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-amber-600">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-white text-amber-600 mr-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Clientes</h3>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Analiza el comportamiento de tus clientes, identifica los más valiosos y comprende sus patrones de compra.</p>
                <a href="{{ route('admin.reports.customers') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded">
                    Ver Reporte
                </a>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-800">
            <h3 class="text-lg font-semibold text-white">Vista Rápida</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Ventas recientes -->
            <div>
                <h4 class="text-lg font-medium text-gray-800 mb-4">Ventas de los Últimos 7 Días</h4>
                <div class="h-64">
                    <canvas id="recentSalesChart"></canvas>
                </div>
            </div>
            
            <!-- Distribución de categorías -->
            <div>
                <h4 class="text-lg font-medium text-gray-800 mb-4">Ventas por Categoría</h4>
                <div class="h-64">
                    <canvas id="categorySalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Productos más vendidos -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Productos Más Vendidos</h3>
            </div>
            <div class="p-6">
                <div id="top-products-container">
                    <div class="flex items-center justify-between pb-2 border-b">
                        <div class="font-medium">Producto</div>
                        <div class="font-medium">Ventas</div>
                    </div>
                    <!-- Aquí se cargarían dinámicamente los productos más vendidos -->
                    <div class="text-center py-4 text-gray-500">
                        Cargando productos...
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.reports.products') }}" class="text-indigo-600 hover:text-indigo-800">
                        Ver todos los productos
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas de clientes -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Estadísticas de Clientes</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-center h-64">
                    <canvas id="customerStatsChart"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.reports.customers') }}" class="text-indigo-600 hover:text-indigo-800">
                        Ver todos los clientes
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Estos son datos de ejemplo. En un entorno real, deberían venir del backend
        
        // Datos para el gráfico de ventas recientes
        const recentSalesData = {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Ventas ($)',
                data: [1200, 1900, 800, 1600, 2000, 2400, 1800],
                backgroundColor: 'rgba(79, 70, 229, 0.2)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        };
        
        // Crear gráfico de ventas recientes
        const ctxRecentSales = document.getElementById('recentSalesChart').getContext('2d');
        const recentSalesChart = new Chart(ctxRecentSales, {
            type: 'line',
            data: recentSalesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
        
        // Datos para el gráfico de ventas por categoría
        const categorySalesData = {
            labels: ['Ropa', 'Electrónica', 'Hogar', 'Deportes', 'Belleza'],
            datasets: [{
                label: 'Ventas por Categoría',
                data: [35, 25, 20, 15, 5],
                backgroundColor: [
                    'rgba(79, 70, 229, 0.7)',
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(239, 68, 68, 0.7)',
                    'rgba(107, 114, 128, 0.7)'
                ],
                borderWidth: 1
            }]
        };
        
        // Crear gráfico de ventas por categoría
        const ctxCategorySales = document.getElementById('categorySalesChart').getContext('2d');
        const categorySalesChart = new Chart(ctxCategorySales, {
            type: 'doughnut',
            data: categorySalesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
        
        // Datos para el gráfico de estadísticas de clientes
        const customerStatsData = {
            labels: ['Nuevos', 'Recurrentes'],
            datasets: [{
                label: 'Clientes',
                data: [65, 35],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(79, 70, 229, 0.7)'
                ],
                borderWidth: 1
            }]
        };
        
        // Crear gráfico de estadísticas de clientes
        const ctxCustomerStats = document.getElementById('customerStatsChart').getContext('2d');
        const customerStatsChart = new Chart(ctxCustomerStats, {
            type: 'pie',
            data: customerStatsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Distribución de Clientes'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
        
        // Simulación de carga dinámica de productos más vendidos
        setTimeout(function() {
            const topProductsContainer = document.getElementById('top-products-container');
            let topProductsHTML = `
                <div class="flex items-center justify-between pb-2 border-b">
                    <div class="font-medium">Producto</div>
                    <div class="font-medium">Ventas</div>
                </div>
            `;
            
            // Datos de ejemplo
            const topProducts = [
                { name: 'Smartphone XYZ', sales: 142 },
                { name: 'Laptop Ultra Delgada', sales: 98 },
                { name: 'Auriculares Inalámbricos', sales: 87 },
                { name: 'Smart TV 55"', sales: 76 },
                { name: 'Tablet Pro', sales: 63 }
            ];
            
            topProducts.forEach(product => {
                topProductsHTML += `
                    <div class="flex items-center justify-between py-3 border-b">
                        <div class="text-gray-800">${product.name}</div>
                        <div class="text-gray-600">${product.sales} uds.</div>
                    </div>
                `;
            });
            
            topProductsContainer.innerHTML = topProductsHTML;
        }, 1000);
    });
</script>
@endpush