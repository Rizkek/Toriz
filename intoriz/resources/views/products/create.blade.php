@extends('layouts.app')

@section('title', 'Add Product')
@section('page-title', 'Add New Product')

@section('content')
    <div class="max-w-5xl mx-auto">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- General Info Card -->
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6 flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center mr-3">
                                <span class="material-icons text-indigo-400 text-sm">info</span>
                            </div>
                            General Information
                        </h3>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Product Name <span
                                        class="text-red-400">*</span></label>
                                <input type="text" name="name"
                                    class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-gray-100 placeholder-gray-600"
                                    placeholder="e.g., Kopi Kapal Api 250g" required>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">SKU / Code <span
                                            class="text-red-400">*</span></label>
                                    <input type="text" name="sku"
                                        class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-gray-100 uppercase font-mono"
                                        placeholder="Generated if empty">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Barcode</label>
                                    <div class="relative">
                                        <input type="text" name="barcode"
                                            class="w-full bg-slate-900/50 border border-slate-700 rounded-xl pl-4 pr-10 py-2.5 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-gray-100 font-mono"
                                            placeholder="Scan or type...">
                                        <button type="button"
                                            class="absolute right-2 top-2 p-1 text-gray-400 hover:text-white transition-colors">
                                            <span class="material-icons text-xl">qr_code_scanner</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Category <span
                                            class="text-red-400">*</span></label>
                                    <div class="relative">
                                        <select name="category_id"
                                            class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-gray-100 appearance-none cursor-pointer"
                                            required>
                                            <option value="">Select Category</option>
                                            @foreach(\App\Models\Category::all() as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        <span
                                            class="material-icons absolute right-3 top-3 text-gray-500 pointer-events-none">expand_more</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Supplier <span
                                            class="text-red-400">*</span></label>
                                    <div class="relative">
                                        <select name="supplier_id"
                                            class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-gray-100 appearance-none cursor-pointer"
                                            required>
                                            <option value="">Select Supplier</option>
                                            @foreach(\App\Models\Supplier::all() as $sup)
                                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                            @endforeach
                                        </select>
                                        <span
                                            class="material-icons absolute right-3 top-3 text-gray-500 pointer-events-none">expand_more</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Info Card -->
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6 flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center mr-3">
                                <span class="material-icons text-emerald-400 text-sm">attach_money</span>
                            </div>
                            Pricing Strategy
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Cost Price <span
                                        class="text-red-400">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-4 top-2.5 text-gray-500">Rp</span>
                                    <input type="number" name="cost_price"
                                        class="w-full bg-slate-900/50 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all text-gray-100 placeholder-gray-600"
                                        placeholder="0" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Selling Price <span
                                        class="text-red-400">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-4 top-2.5 text-gray-500">Rp</span>
                                    <input type="number" name="price"
                                        class="w-full bg-slate-900/50 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all text-gray-100 placeholder-gray-600"
                                        placeholder="0" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Stock & Images -->
                <div class="space-y-6">
                    <!-- Stock Info Card -->
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6 flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-orange-500/20 flex items-center justify-center mr-3">
                                <span class="material-icons text-orange-400 text-sm">inventory</span>
                            </div>
                            Inventory
                        </h3>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Unit <span
                                        class="text-red-400">*</span></label>
                                <input type="text" name="unit"
                                    class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all text-gray-100"
                                    placeholder="pcs, kg, box" value="pcs" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Initial Stock</label>
                                    <input type="number" name="current_stock"
                                        class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all text-gray-100"
                                        value="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Min Stock <span
                                            class="text-red-400">*</span></label>
                                    <input type="number" name="min_stock"
                                        class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all text-gray-100"
                                        value="5" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Expiry Date</label>
                                <input type="date" name="expiry_date"
                                    class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all text-gray-100">
                            </div>
                        </div>
                    </div>

                    <!-- Images Card -->
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
                        <label class="block text-sm font-bold text-white mb-4">Product Images</label>
                        <div
                            class="border-2 border-dashed border-slate-600 rounded-xl p-6 text-center hover:border-indigo-500 hover:bg-slate-800/80 transition-all cursor-pointer relative group">
                            <input type="file" name="images[]" multiple
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="group-hover:scale-105 transition-transform duration-300">
                                <span
                                    class="material-icons text-4xl text-gray-500 mb-2 group-hover:text-indigo-400">cloud_upload</span>
                                <p class="text-gray-400 text-sm font-medium">Click or drag images here</p>
                                <p class="text-gray-600 text-xs mt-1">PNG, JPG up to 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end items-center space-x-4 pt-4 border-t border-slate-700/50">
                <a href="{{ route('products.index') }}"
                    class="px-6 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-slate-800 transition-colors font-medium">Cancel</a>
                <button type="submit"
                    class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-all font-bold flex items-center">
                    <span class="material-icons text-xl mr-2">save</span>
                    Save Product
                </button>
            </div>
        </form>
    </div>
@endsection