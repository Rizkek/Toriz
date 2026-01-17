@extends('layouts.app')

@section('title', 'Manage Stock')
@section('page-title', 'Stock Transactions')

@section('content')
    <div class="space-y-6">
        <!-- Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stock In -->
            <div class="bg-gradient-to-br from-green-600/20 to-emerald-600/20 backdrop-blur-xl border border-green-500/30 rounded-2xl p-6 hover:border-green-500/50 transition-all group cursor-pointer"
                onclick="document.getElementById('stock-in-modal').showModal()">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-green-500/20 flex items-center justify-center group-hover:bg-green-500 group-hover:text-white transition-colors text-green-400">
                        <span class="material-icons text-3xl">add_circle</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Stock In</h3>
                        <p class="text-sm text-green-200/70">Record incoming goods</p>
                    </div>
                </div>
            </div>

            <!-- Stock Out -->
            <div class="bg-gradient-to-br from-red-600/20 to-orange-600/20 backdrop-blur-xl border border-red-500/30 rounded-2xl p-6 hover:border-red-500/50 transition-all group cursor-pointer"
                onclick="document.getElementById('stock-out-modal').showModal()">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-red-500/20 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-colors text-red-400">
                        <span class="material-icons text-3xl">remove_circle</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Stock Out</h3>
                        <p class="text-sm text-red-200/70">Record sales or usage</p>
                    </div>
                </div>
            </div>

            <!-- Adjustment -->
            <div class="bg-gradient-to-br from-slate-600/20 to-gray-600/20 backdrop-blur-xl border border-slate-500/30 rounded-2xl p-6 hover:border-slate-500/50 transition-all group cursor-pointer"
                onclick="alert('Use product edit page for adjustments for now')">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-slate-500/20 flex items-center justify-center group-hover:bg-slate-500 group-hover:text-white transition-colors text-slate-400">
                        <span class="material-icons text-3xl">tune</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Adjustment</h3>
                        <p class="text-sm text-slate-300/70">Correct discrepancies</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions List -->
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-700/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Transaction History</h3>
                <div class="flex space-x-2">
                    <select
                        class="bg-slate-900 border border-slate-700 rounded-lg text-sm px-3 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="all">All Types</option>
                        <option value="in">In</option>
                        <option value="out">Out</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-700/30 text-xs uppercase text-gray-400">
                            <th class="px-6 py-4 font-semibold">Date</th>
                            <th class="px-6 py-4 font-semibold">Product</th>
                            <th class="px-6 py-4 font-semibold">Type</th>
                            <th class="px-6 py-4 font-semibold text-right">Quantity</th>
                            <th class="px-6 py-4 font-semibold">Reference</th>
                            <th class="px-6 py-4 font-semibold">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-slate-700/20 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    {{ $transaction->transaction_date->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-white">{{ $transaction->product->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $transaction->product->sku }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $transaction->type === 'in' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span
                                        class="font-bold {{ $transaction->type === 'in' ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $transaction->type === 'in' ? '+' : '-' }}{{ abs($transaction->quantity) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">
                                    {{ $transaction->reference_type ?? '-' }} #{{ $transaction->reference_id }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">
                                    {{ $transaction->notes }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-700/50">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    <!-- Simple Modals (To be replaced with Livewire Components later) -->
    <dialog id="stock-in-modal" class="bg-transparent backdrop:bg-slate-900/80 p-0 rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="bg-slate-800 border border-slate-700 text-gray-100 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center">
                <h3 class="text-xl font-bold">Stock In</h3>
                <button onclick="document.getElementById('stock-in-modal').close()" class="text-gray-400 hover:text-white">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <form action="{{ route('stock.in') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Product</label>
                    <select name="product_id"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 focus:ring-2 focus:ring-green-500"
                        required>
                        <option value="">Select Product...</option>
                        @foreach(\App\Models\Product::active()->get() as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Cur: {{ $p->current_stock }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="1"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 focus:ring-2 focus:ring-green-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-xl font-medium transition-colors">
                        Save Transaction
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="stock-out-modal" class="bg-transparent backdrop:bg-slate-900/80 p-0 rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="bg-slate-800 border border-slate-700 text-gray-100 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-red-400">Stock Out</h3>
                <button onclick="document.getElementById('stock-out-modal').close()" class="text-gray-400 hover:text-white">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <form action="{{ route('stock.out') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Product</label>
                    <select name="product_id"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 focus:ring-2 focus:ring-red-500"
                        required>
                        <option value="">Select Product...</option>
                        @foreach(\App\Models\Product::active()->get() as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Cur: {{ $p->current_stock }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="1"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 focus:ring-2 focus:ring-red-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 focus:ring-2 focus:ring-red-500"></textarea>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-xl font-medium transition-colors">
                        Save Transaction
                    </button>
                </div>
            </form>
        </div>
    </dialog>
@endsection