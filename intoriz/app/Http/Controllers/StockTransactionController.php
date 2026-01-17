<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
    public function __construct(
        private StockService $stockService
    ) {
    }

    public function index(Request $request)
    {
        $transactions = StockTransaction::with(['product', 'user'])
            ->when($request->product_id, function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })
            ->when($request->type, function ($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('transaction_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('transaction_date', '<=', $request->date_to);
            })
            ->latest('transaction_date')
            ->paginate(50);

        $products = Product::active()->get();

        return view('stock.index', compact('transactions', 'products'));
    }

    public function stockIn(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $this->stockService->stockIn(
            $product,
            $validated['quantity'],
            'Manual',
            null,
            $validated['notes'] ?? null
        );

        return back()->with('success', 'Stock in recorded successfully');
    }

    public function stockOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        try {
            $this->stockService->stockOut(
                $product,
                $validated['quantity'],
                'Manual',
                null,
                $validated['notes'] ?? null
            );

            return back()->with('success', 'Stock out recorded successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'new_quantity' => 'required|integer|min:0',
            'notes' => 'required|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $this->stockService->adjustStock(
            $product,
            $validated['new_quantity'],
            $validated['notes']
        );

        return back()->with('success', 'Stock adjusted successfully');
    }
}
