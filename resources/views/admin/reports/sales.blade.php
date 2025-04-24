@extends('admin.layouts.app')

@section('header', 'Reporte de Ventas')

@section('content')
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-6">
                <form action="{{ route('admin.reports.sales') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="group_by" class="block text-sm font-medium text-gray-700 mb-1">Agrupar por</label>
                        <select name="group_by" id="group_by" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>Día</option>
                            <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>Semana</option>
                            <option value="month" {{ $groupBy == 'month' ? 'selected' : '' }}>Mes</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Generar Reporte
                        </button>
                    </div>
                    <div class="ml-auto">
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <a href="{{ route('admin.reports.export', ['type' => 'sales', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'format' => 'csv']) }}" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-l-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                CSV
                            </a>
                            <a href="{{ route('admin.reports.export', ['type' => 'sales', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'format' => 'xlsx']) }}" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                Excel
                            </a>
                            <a href="{{ route('admin.reports.export', ['type' => 'sales', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'format' => 'pdf']) }}" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-r-md border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                PDF
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg border p-4">
                    <div class="text-gray-500 text-sm">Ventas Totales</div>
                    <div class="text-2xl font-bold text-gray-800">${{ number_format($report['totals']['revenue'], 2) }}</div>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <div class="text-gray-500 text-sm">Número de Pedidos</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($report['totals']['orders_count']) }}</div>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <div class="text-gray-500 text-sm">Subtotal</div>
                    <div class="text-2xl font-bold text-gray-800">${{ number_format($report['totals']['subtotal'], 2) }}</div>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <div class="text-gray-500 text-sm">Impuestos</div>
                    <div class="text-2xl font-bold text-gray-800">${{ number_format($report['totals']['tax'], 2) }}</div>
                </div>
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Gráfico de Ventas</h3>
                <div class="h-80">
                    <canvas id="salesReportChart"></canvas>
                </div>
            </div>
            
            <div>
            // Continuación de resources/views/admin/reports/sales.blade.php
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalle de Ventas</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pedidos</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Impuestos</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Envío</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($report['data'] as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->orders_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->subtotal, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->tax, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->shipping, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->discount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format($item->revenue, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <th scope="row" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <td class="px-6 py-3 text-left text-xs font-medium text-gray-900">{{ $report['totals']['orders_count'] }}</td>
                                <td class="px-6 py-3 text-left text-xs font-medium text-gray-900">${{ number_format($report['totals']['subtotal'], 2) }}</td>
                                <td class="px-6 py-3 text-left text-xs font-medium text-gray-900">${{ number_format($report['totals']['tax'], 2) }}</td>
                                <td class="px-6 py-3 text-left text-xs font-medium text-gray-900">${{ number_format($report['totals']['shipping'], 2) }}</td>
                                <td class="px-6 py-3 text-left text-xs font-medium text-gray-900">${{ number_format($report['totals']['discount'], 2) }}</td>
                                <td class="px-6 py-3 text-left text-xs font-medium text-gray-900">${{ number_format($report['totals']['revenue'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para el gráfico de ventas
        const salesData = @json($report['data']);
        
        // Preparar datos para Chart.js
        const labels = salesData.map(item => item.date);
        const revenues = salesData.map(item => item.revenue);
        const ordersCount = salesData.map(item => item.orders_count);
        
        // Crear el gráfico
        const ctx = document.getElementById('salesReportChart').getContext('2d');
        const salesReportChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ventas ($)',
                        data: revenues,
                        backgroundColor: 'rgba(79, 70, 229, 0.2)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Número de Pedidos',
                        data: ordersCount,
                        type: 'line',
                        fill: false,
                        backgroundColor: 'rgba(245, 158, 11, 1)',
                        borderColor: 'rgba(245, 158, 11, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(245, 158, 11, 1)',
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        type: 'linear',
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Ventas ($)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        type: 'linear',
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Número de Pedidos'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    label += '$' + context.parsed.y.toFixed(2);
                                } else {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush