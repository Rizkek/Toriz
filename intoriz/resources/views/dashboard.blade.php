@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Products -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm font-medium">Total Products</p>
                        <h3 class="text-3xl font-bold text-white mt-2">{{ number_format($summary['total_products']) }}</h3>
                    </div>
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-icons text-white text-3xl">inventory_2</span>
                    </div>
                </div>
            </div>

            <!-- Stock Value -->
            <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-200 text-sm font-medium">Stock Value</p>
                        <h3 class="text-3xl font-bold text-white mt-2">Rp {{ number_format($summary['total_stock_value']) }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-icons text-white text-3xl">attach_money</span>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="bg-gradient-to-br from-orange-600 to-orange-800 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-200 text-sm font-medium">Low Stock</p>
                        <h3 class="text-3xl font-bold text-white mt-2">{{ number_format($summary['low_stock_count']) }}</h3>
                    </div>
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-icons text-white text-3xl">warning</span>
                    </div>
                </div>
            </div>

            <!-- Out of Stock -->
            <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-200 text-sm font-medium">Out of Stock</p>
                        <h3 class="text-3xl font-bold text-white mt-2">{{ number_format($summary['out_of_stock_count']) }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-icons text-white text-3xl">remove_shopping_cart</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Stock Movement Chart -->
            <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white">Stock Movement (Last 7 Days)</h3>
                    <span class="material-icons text-gray-400">timeline</span>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="stockMovementChart"></canvas>
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white">Low Stock Alert</h3>
                    <a href="{{ route('reports.low-stock') }}"
                        class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">View All</a>
                </div>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @forelse($lowStockProducts as $product)
                        <div
                            class="flex items-center justify-between p-3 bg-slate-700/30 rounded-lg border border-slate-600/50 hover:border-orange-500/50 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">{{ $product->name }}</p>
                                <p class="text-xs text-gray-400">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            </div>
                            <div class="ml-4 flex items-center space-x-2">
                                <span class="px-3 py-1 text-xs font-semibold bg-orange-500/20 text-orange-300 rounded-lg">
                                    {{ $product->current_stock }} {{ $product->unit }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <span class="material-icons text-4xl mb-2">check_circle</span>
                            <p>All products have sufficient stock</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Transactions -->
            <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white">Recent Transactions</h3>
                    <a href="{{ route('stock.transactions') }}"
                        class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">View All</a>
                </div>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($recentTransactions as $transaction)
                        <div
                            class="flex items-center justify-between p-3 bg-slate-700/30 rounded-lg border border-slate-600/50">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div
                                    class="w-10 h-10 rounded-lg flex items-center justify-center
                                        {{ $transaction->type === 'in' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                    <span class="material-icons text-xl">
                                        {{ $transaction->type === 'in' ? 'add' : 'remove' }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">{{ $transaction->product->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $transaction->transaction_date->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            <span class="ml-4 text-sm font-semibold 
                                    {{ $transaction->type === 'in' ? 'text-green-400' : 'text-red-400' }}">
                                {{ $transaction->type === 'in' ? '+' : '' }}{{ $transaction->quantity }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <span class="material-icons text-4xl mb-2">history</span>
                            <p>No transactions yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Expiring Products -->
            <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white">Expiring Soon</h3>
                    <a href="{{ route('reports.expiry') }}"
                        class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">View All</a>
                </div>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($expiringProducts as $product)
                        <div
                            class="flex items-center justify-between p-3 bg-slate-700/30 rounded-lg border border-slate-600/50 hover:border-yellow-500/50 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">{{ $product->name }}</p>
                                <p class="text-xs text-gray-400">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            </div>
                            <div class="ml-4">
                                <span class="px-3 py-1 text-xs font-semibold bg-yellow-500/20 text-yellow-300 rounded-lg">
                                    {{ $product->expiry_date->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <span class="material-icons text-4xl mb-2">check_circle</span>
                            <p>No products expiring soon</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-white mb-6">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('products.create') }}"
                    class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl hover:shadow-lg hover:shadow-indigo-500/50 transition-all">
                    <span class="material-icons text-4xl mb-2">add_box</span>
                    <span class="text-sm font-medium">Add Product</span>
                </a>
                <a href="{{ route('stock.transactions') }}"
                    class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-green-600 to-emerald-600 rounded-xl hover:shadow-lg hover:shadow-green-500/50 transition-all">
                    <span class="material-icons text-4xl mb-2">system_update_alt</span>
                    <span class="text-sm font-medium">Stock In</span>
                </a>
                <a href="{{ route('stock.transactions') }}"
                    class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-orange-600 to-red-600 rounded-xl hover:shadow-lg hover:shadow-orange-500/50 transition-all">
                    <span class="material-icons text-4xl mb-2">local_shipping</span>
                    <span class="text-sm font-medium">Stock Out</span>
                </a>
                <a href="{{ route('reports.stock-value') }}"
                    class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl hover:shadow-lg hover:shadow-blue-500/50 transition-all">
                    <span class="material-icons text-4xl mb-2">assessment</span>
                    <span class="text-sm font-medium">Reports</span>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Stock Movement Chart
        const ctx = document.getElementById('stockMovementChart').getContext('2d');
        const chartData = @json($stockMovementData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => new Date(d.date).toLocaleDateString('id-ID', { month: 'short', day: 'numeric' })),
                datasets: [
                    {
                        label: 'Stock In',
                        data: chartData.map(d => d.stock_in),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Stock Out',
                        data: chartData.map(d => d.stock_out),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: { color: '#e2e8f0' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#94a3b8' },
                        grid: { color: 'rgba(148, 163, 184, 0.1)' }
                    },
                    x: {
                        ticks: { color: '#94a3b8' },
                        grid: { color: 'rgba(148, 163, 184, 0.1)' }
                    }
                }
            }
        });
    </script>
@endpush