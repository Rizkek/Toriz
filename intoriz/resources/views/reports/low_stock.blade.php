@extends('layouts.app')

@section('title', 'Low Stock Report')
@section('page-title', 'Low Stock Alert')

@section('content')
    <div class="space-y-6">
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-orange-500/20 flex items-center justify-center">
                    <span class="material-icons text-orange-400 text-2xl">warning</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Low Stock Products</h3>
                    <p class="text-gray-400">Products currently below minimum stock level</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-700/30 text-xs uppercase text-gray-400">
                            <th class="px-6 py-4 font-semibold">Product Name</th>
                            <th class="px-6 py-4 font-semibold">SKU</th>
                            <th class="px-6 py-4 font-semibold">Category</th>
                            <th class="px-6 py-4 font-semibold text-right">Current Stock</th>
                            <th class="px-6 py-4 font-semibold text-right">Min Stock</th>
                            <th class="px-6 py-4 font-semibold text-center">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-700/20 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-white">{{ $product->name }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">{{ $product->sku }}</td>
                                <td class="px-6 py-4 text-sm text-indigo-400">{{ $product->category->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-orange-400">{{ $product->current_stock }}</span>
                                    <span class="text-xs text-gray-500">{{ $product->unit }}</span>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-400">{{ $product->min_stock }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($product->current_stock == 0)
                                        <span class="px-2 py-1 rounded-lg bg-red-500/20 text-red-400 text-xs font-bold">Out of
                                            Stock</span>
                                    @else
                                        <span class="px-2 py-1 rounded-lg bg-orange-500/20 text-orange-400 text-xs font-bold">Low
                                            Stock</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Restock</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <span class="material-icons text-4xl mb-2 text-green-500/50">check_circle</span>
                                        <p>All active products have sufficient stock levels.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection