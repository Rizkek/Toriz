@extends('layouts.app')

@section('title', 'Stock Value')
@section('page-title', 'Stock Value Report')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl p-8 text-center shadow-xl">
                <p class="text-indigo-200 font-medium mb-2">Total Inventory Value</p>
                <h2 class="text-4xl font-extrabold text-white">Rp {{ number_format($totalValue, 0, ',', '.') }}</h2>
                <p class="text-indigo-200/60 text-sm mt-2">Based on Cost Price</p>
            </div>

            <div class="bg-gradient-to-br from-green-600 to-emerald-600 rounded-2xl p-8 text-center shadow-xl">
                <p class="text-green-200 font-medium mb-2">Potential Sales Value</p>
                <h2 class="text-4xl font-extrabold text-white">Rp {{ number_format($potentialValue, 0, ',', '.') }}</h2>
                <p class="text-green-200/60 text-sm mt-2">Based on Selling Price</p>
            </div>
        </div>

        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-700/50">
                <h3 class="text-lg font-bold text-white">Value by Category</h3>
            </div>
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-700/30 text-xs uppercase text-gray-400">
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4 text-right">Product Count</th>
                        <th class="px-6 py-4 text-right">Total Items</th>
                        <th class="px-6 py-4 text-right">Total Value (Cost)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @foreach($valueByCategory as $cat)
                        <tr class="hover:bg-slate-700/20">
                            <td class="px-6 py-4 font-medium text-white">{{ $cat->name }}</td>
                            <td class="px-6 py-4 text-right text-gray-400">{{ $cat->products_count }}</td>
                            <td class="px-6 py-4 text-right text-gray-300">{{ number_format($cat->total_items) }}</td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-400">
                                Rp {{ number_format($cat->total_value, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection