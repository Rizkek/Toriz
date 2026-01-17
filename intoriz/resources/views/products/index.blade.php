@extends('layouts.app')

@section('title', 'Products')
@section('page-title', 'Product Management')

@section('content')
    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-4 flex-1">
                <div class="relative flex-1 max-w-md">
                    <input type="search" placeholder="Search products..."
                        class="w-full pl-10 pr-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 text-sm">
                    <span class="material-icons absolute left-3 top-2 text-gray-400 text-lg">search</span>
                </div>
                <div class="relative">
                    <select
                        class="pl-3 pr-8 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl appearance-none text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <span
                        class="material-icons absolute right-2 top-2 text-gray-400 text-lg pointer-events-none">expand_more</span>
                </div>
            </div>

            <a href="{{ route('products.create') }}"
                class="flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-colors shadow-lg shadow-indigo-500/30">
                <span class="material-icons text-xl mr-2">add</span>
                <span>New Product</span>
            </a>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($products as $product)
                <div
                    class="group bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden hover:border-indigo-500/50 transition-all hover:shadow-xl hover:shadow-indigo-500/10">
                    <!-- Image/Icon -->
                    <div class="h-48 bg-slate-700/50 relative overflow-hidden">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-600">
                                <span class="material-icons text-6xl">inventory_2</span>
                            </div>
                        @endif

                        <!-- Status Badges -->
                        <div class="absolute top-3 left-3 flex flex-col gap-2">
                            @if($product->current_stock <= $product->min_stock)
                                <span
                                    class="px-2 py-1 bg-red-500/90 text-white text-xs font-bold rounded-lg backdrop-blur-sm shadow-sm">
                                    Low Stock
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5">
                        <div class="mb-4">
                            <div class="text-xs text-indigo-400 font-medium mb-1">
                                {{ $product->category->name ?? 'Uncategorized' }}</div>
                            <h3 class="text-lg font-bold text-white truncate" title="{{ $product->name }}">{{ $product->name }}
                            </h3>
                            <p class="text-sm text-gray-400 truncate">SKU: {{ $product->sku }}</p>
                        </div>

                        <div class="flex items-end justify-between mb-4">
                            <div>
                                <p class="text-xs text-gray-400">Price</p>
                                <p class="text-lg font-bold text-white">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">Stock</p>
                                <p
                                    class="text-lg font-bold {{ $product->current_stock <= $product->min_stock ? 'text-red-400' : 'text-green-400' }}">
                                    {{ $product->current_stock }} <span
                                        class="text-sm font-normal text-gray-500">{{ $product->unit }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('products.edit', $product) }}"
                                class="flex items-center justify-center px-3 py-2 bg-slate-700/50 hover:bg-slate-600/50 rounded-lg text-sm text-white transition-colors">
                                <span class="material-icons text-sm mr-2">edit</span>
                                Edit
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full flex items-center justify-center px-3 py-2 bg-slate-700/50 hover:bg-red-500/20 hover:text-red-400 rounded-lg text-sm text-gray-300 transition-colors">
                                    <span class="material-icons text-sm mr-2">delete</span>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-12 text-gray-500">
                    <span class="material-icons text-6xl mb-4">search_off</span>
                    <p class="text-lg">No products found</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
@endsection