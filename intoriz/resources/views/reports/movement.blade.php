@extends('layouts.app')

@section('title', 'Stock Movement')
@section('page-title', 'Stock Movement Report')

@section('content')
    <div class="space-y-6">
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-6">Daily Stock Movement</h3>

            <div class="h-80 w-full">
                <canvas id="movementChart"></canvas>
            </div>
        </div>

        <!-- Detailed List -->
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-700/50">
                <h3 class="text-lg font-bold text-white">Daily Breakdown</h3>
            </div>
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-700/30 text-xs uppercase text-gray-400">
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-green-400">Total In</th>
                        <th class="px-6 py-4 text-red-400">Total Out</th>
                        <th class="px-6 py-4">Net Change</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @foreach($dailyMovements as $movement)
                        <tr class="hover:bg-slate-700/20">
                            <td class="px-6 py-4 text-gray-300">{{ \Carbon\Carbon::parse($movement->date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-green-400 font-bold">+{{ $movement->stock_in }}</td>
                            <td class="px-6 py-4 text-red-400 font-bold">-{{ $movement->stock_out }}</td>
                            <td
                                class="px-6 py-4 font-bold {{ ($movement->stock_in - $movement->stock_out) >= 0 ? 'text-blue-400' : 'text-orange-400' }}">
                                {{ $movement->stock_in - $movement->stock_out }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('movementChart').getContext('2d');
        const data = @json($dailyMovements);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => new Date(d.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })),
                datasets: [
                    {
                        label: 'Stock In',
                        data: data.map(d => d.stock_in),
                        backgroundColor: 'rgba(34, 197, 94, 0.5)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    },
                    {
                        label: 'Stock Out',
                        data: data.map(d => d.stock_out),
                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#94a3b8' } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                },
                plugins: { legend: { labels: { color: '#e2e8f0' } } }
            }
        });
    </script>
@endpush