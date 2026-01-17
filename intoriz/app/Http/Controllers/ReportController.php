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

        $transactions = $query->get();
        $products = Product::active()->get();

        return view('reports.stock-movement', compact('transactions', 'products'));
    }

    public function lowStock()
    {
        $lowStockProducts = Product::active()
            ->lowStock()
            ->with(['category', 'supplier'])
            ->get()
            ->map(function ($product) {
                $product->stock_percentage = $product->min_stock > 0
                    ? ($product->current_stock / $product->min_stock) * 100
                    : 0;
                return $product;
            });

        return view('reports.low-stock', compact('lowStockProducts'));
    }

    public function expiry(Request $request)
    {
        $days = $request->input('days', 30);

        $expiringProducts = Product::active()
            ->expiringSoon($days)
            ->with(['category', 'supplier'])
            ->orderBy('expiry_date')
            ->get();

        $expiredProducts = Product::active()
            ->expired()
            ->with(['category', 'supplier'])
            ->get();

        return view('reports.expiry', compact('expiringProducts', 'expiredProducts', 'days'));
    }

    public function productPerformance(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30));
        $dateTo = $request->input('date_to', now());

        // Top selling products (most stock out)
        $topProducts = StockTransaction::select('product_id')
            ->selectRaw('SUM(ABS(quantity)) as total_sold')
            ->where('type', 'out')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->with('product.category')
            ->get();

        // Slow moving products (low turnover)
        $slowMovingProducts = Product::active()
            ->whereDoesntHave('stockTransactions', function ($q) use ($dateFrom, $dateTo) {
                $q->where('type', 'out')
                    ->whereBetween('transaction_date', [$dateFrom, $dateTo]);
            })
            ->with('category')
            ->get();

        return view('reports.product-performance', compact('topProducts', 'slowMovingProducts', 'dateFrom', 'dateTo'));
    }

    public function stockValue()
    {
        $totalValue = Product::active()
            ->sum(DB::raw('current_stock * cost_price'));

        $valueByCategory = Product::active()
            ->select('categories.name as category_name')
            ->selectRaw('SUM(products.current_stock * products.cost_price) as total_value')
            ->selectRaw('SUM(products.current_stock) as total_qty')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_value')
            ->get();

        return view('reports.stock-value', compact('totalValue', 'valueByCategory'));
    }
}
