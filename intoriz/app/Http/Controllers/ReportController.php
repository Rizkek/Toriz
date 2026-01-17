<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function stockMovement(Request $request)
    {
        $query = StockTransaction::with(['product.category', 'user'])
            ->when($request->date_from, fn($q) => $q->whereDate('transaction_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('transaction_date', '<=', $request->date_to))
            ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
            ->latest('transaction_date');

        $transactions = $query->paginate(20); // Add pagination
        $products = Product::active()->orderBy('name')->get();

        // Calculate Daily Movements for Chart
        $dailyMovements = StockTransaction::selectRaw('DATE(transaction_date) as date')
            ->selectRaw("SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) as stock_in")
            ->selectRaw("SUM(CASE WHEN type = 'out' THEN ABS(quantity) ELSE 0 END) as stock_out")
            ->where('transaction_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.movement', compact('transactions', 'products', 'dailyMovements'));
    }

    public function lowStock()
    {
        $products = Product::active()
            ->lowStock()
            ->with(['category', 'supplier'])
            ->get();

        return view('reports.low_stock', compact('products'));
    }

    public function expiry(Request $request)
    {
        $days = $request->input('days', 30);

        $products = Product::active()
            ->expiringSoon($days)
            ->with(['category', 'supplier'])
            ->orderBy('expiry_date')
            ->get();

        return view('reports.expiry', compact('products', 'days'));
    }

    public function stockValue()
    {
        $totalValue = Product::active()
            ->sum(DB::raw('current_stock * cost_price'));

        $potentialValue = Product::active()
            ->sum(DB::raw('current_stock * price'));

        $valueByCategory = Product::query()
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                DB::raw('COUNT(products.id) as products_count'),
                DB::raw('SUM(products.current_stock) as total_items'),
                DB::raw('SUM(products.current_stock * products.cost_price) as total_value')
            )
            ->where('products.is_active', 1)
            ->whereNull('products.deleted_at')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_value')
            ->get();

        return view('reports.value', compact('totalValue', 'potentialValue', 'valueByCategory'));
    }
}
