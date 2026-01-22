@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- KPI Cards Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Stock Value -->
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-semibold tracking-wide">STOCK VALUE</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($summary['total_stock_value'] ?? 0) }}</h3>
                    </div>
                    <div class="p-2 bg-teal-50 rounded-lg">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-semibold tracking-wide">TOTAL PRODUCTS</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['total_products'] ?? 0) }}</h3>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m0 0v10l8 4M4 7L2.5 5.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-semibold tracking-wide">LOW STOCK</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['low_stock_count'] ?? 0) }}</h3>
                    </div>
                    <div class="p-2 bg-orange-50 rounded-lg">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M6.34 8.66L4.93 7.25M9 5h6M4.93 16.75l1.41 1.41M19.07 4.93l1.41 1.41M19.07 19.07l1.41 1.41" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Out of Stock -->
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-semibold tracking-wide">OUT OF STOCK</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['out_of_stock_count'] ?? 0) }}</h3>
                    </div>
                    <div class="p-2 bg-red-50 rounded-lg">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v2m-6-6h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Transactions Table (60% width) -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
                        <a href="{{ route('stock.transactions') }}" class="text-sm font-medium text-teal-600 hover:text-teal-700">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($recentTransactions as $transaction)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-gray-900 font-medium">{{ $transaction->product->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700">{{ number_format($transaction->quantity) }}</td>
                                        <td class="px-6 py-4 text-gray-600 text-xs">{{ $transaction->created_at->format('M d, H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            <p class="text-sm">No transactions yet</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert Sidebar -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Low Stock Alert</h2>
                </div>
                <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    @forelse($lowStockProducts as $product)
                        <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between mb-1">
                                <p class="font-medium text-gray-900 text-sm">{{ $product->name }}</p>
                                <span class="text-xs font-semibold text-orange-700 bg-orange-100 px-2 py-1 rounded">
                                    {{ $product->current_stock }} {{ $product->unit }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-gray-500">
                            <p class="text-sm">All products have sufficient stock</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Stock Movement Chart -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Stock Movement (Last 7 Days)</h2>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="stockMovementChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('stockMovementChart')?.getContext('2d');
        if (ctx) {
            const chartData = @json($stockMovementData ?? []);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(d => {
                        const date = new Date(d.date);
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }),
                    datasets: [
                        {
                            label: 'Stock In',
                            data: chartData.map(d => d.stock_in || 0),
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.05)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#059669',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Stock Out',
                            data: chartData.map(d => d.stock_out || 0),
                            borderColor: '#dc2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.05)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#dc2626',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { size: 13, weight: 500 },
                                color: '#374151'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#6b7280',
                                font: { size: 12 }
                            },
                            grid: {
                                color: '#e5e7eb',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                color: '#6b7280',
                                font: { size: 12 }
                            },
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
