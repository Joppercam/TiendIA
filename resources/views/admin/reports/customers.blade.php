@extends('admin.layouts.app')

@section('header', 'Reporte de Clientes')

@section('content')
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-6">
                <form action="{{ route('admin.reports.customers') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="limit" class="block text-sm font-medium text-gray-700 mb-1">Límite</label>
                        <input type="number" name="limit" id="limit" min="5" max="100" value="{{ $limit }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-24">
                    </div>
                    <div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Generar Reporte
                        </button>
                    </div>
                    <div class="ml-auto">
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <a href="{{ route('admin.reports.export', ['type' => 'customers', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'format' => 'csv']) }}" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-l-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                CSV
                            </a>
                            <a href="{{ route('admin.reports.export', ['type' => 'customers', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'format' => 'xlsx']) }}" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                Excel
                            </a>
                            <a href="{{ route('admin.reports.export', ['type' => 'customers', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'format' => 'pdf']) }}" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-r-md border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                PDF
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg border p-4">
                    <div class="text-gray-500 text-sm">Clientes Totales</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($report['total_customers']) }}</div>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <div class="text-gray-500 text-sm">Nuevos Clientes</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($report['new_customers']) }}</div>
                    <div class="text-sm text-gray-500">En el período seleccionado</div>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <div class="text-gray-500 text-sm">Valor Medio de Pedido</div>
                    <div class="text-2xl font-bold text-gray-800">${{ number_format($report['top_customers']->avg('average_order_value') ?? 0, 2) }}</div>
                </div>
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Clientes Principales</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div>
                        <div class="h-80">
                            <canvas id="topCustomersChart"></canvas>
                        </div>
                    </div>
                    <div>
                        <div class="h-80">
                            <canvas id="ordersCustomersChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pedidos</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Gastado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Medio</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($report['top_customers'] as $customer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $customer->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $customer->orders_count }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${{ number_format($customer->total_spent, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${{ number_format($customer->average_order_value, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.analytics.customer', $customer->id) }}" class="text-indigo-600 hover:text-indigo-900">Analíticas</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

// Continuación de resources/views/admin/reports/customers.blade.php
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para los gráficos
        const topCustomers = @json($report['top_customers']->take(10));
        
        // Preparar datos para Chart.js
        const customerNames = topCustomers.map(item => item.name);
        const totalSpent = topCustomers.map(item => item.total_spent);
        const ordersCount = topCustomers.map(item => item.orders_count);
        
        // Crear gráfico de clientes por gasto total
        const ctxSpent = document.getElementById('topCustomersChart').getContext('2d');
        const topCustomersChart = new Chart(ctxSpent, {
            type: 'bar',
            data: {
                labels: customerNames,
                datasets: [{
                    label: 'Total Gastado ($)',
                    data: totalSpent,
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
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
                    title: {
                        display: true,
                        text: 'Clientes por gasto total'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.x.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
        
        // Crear gráfico de clientes por número de pedidos
        const ctxOrders = document.getElementById('ordersCustomersChart').getContext('2d');
        const ordersCustomersChart = new Chart(ctxOrders, {
            type: 'bar',
            data: {
                labels: customerNames,
                datasets: [{
                    label: 'Número de Pedidos',
                    data: ordersCount,
                    backgroundColor: 'rgba(245, 158, 11, 0.2)',
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Clientes por número de pedidos'
                    }
                }
            }
        });
    });
</script>
@endpush