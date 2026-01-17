@extends('layouts.app')

@section('title', 'Expiry Report')
@section('page-title', 'Expiry Alert')

@section('content')
    <div class="space-y-6">
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-red-500/20 flex items-center justify-center">
                    <span class="material-icons text-red-400 text-2xl">event_busy</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Expiring Products</h3>
                    <p class="text-gray-400">Products expiring within next 30 days</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-700/30 text-xs uppercase text-gray-400">
                            <th class="px-6 py-4 font-semibold">Product Name</th>
                            <th class="px-6 py-4 font-semibold">Batch / SKU</th>
                            <th class="px-6 py-4 font-semibold">Expiry Date</th>
                            <th class="px-6 py-4 font-semibold">Days Left</th>
                            <th class="px-6 py-4 font-semibold text-right">Stock</th>
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
                                <td class="px-6 py-4 text-sm text-white">{{ $product->expiry_date->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $daysLeft = now()->diffInDays($product->expiry_date, false);
                                    @endphp
                                    @if($daysLeft < 0)
                                        <span class="text-red-500 font-bold">Expired {{ abs(intval($daysLeft)) }} days ago</span>
                                    @else
                                        <span class="text-yellow-500 font-bold">{{ intval($daysLeft) }} days left</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-gray-200">{{ $product->current_stock }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="text-red-400 hover:text-red-300 text-sm font-medium">Write Off</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <span class="material-icons text-4xl mb-2 text-green-500/50">check_circle</span>
                                        <p>No products expiring soon.</p>
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