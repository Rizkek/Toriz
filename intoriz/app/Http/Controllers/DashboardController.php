<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Services\StockService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private StockService $stockService
    ) {
    }

    public function index()
    {
        $summary = $this->stockService->getStockSummary();

        $lowStockProducts = Product::active()
            ->lowStock()
            ->with('category')
            ->limit(10)
            ->get();

        $recentTransactions = StockTransaction::with(['product', 'user'])
            ->latest('transaction_date')
            ->limit(10)
            ->get();

        $expiringProducts = Product::active()
            ->expiringSoon(30)
            ->with('category')
            ->limit(10)
            ->get();

        // Chart data - last 7 days stock movement
        $stockMovementData = StockTransaction::selectRaw('DATE(transaction_date) as date, SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as stock_in, SUM(CASE WHEN type = "out" THEN ABS(quantity) ELSE 0 END) as stock_out')
            ->whereBetween('transaction_date', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard', compact(
            'summary',
            'lowStockProducts',
            'recentTransactions',
            'expiringProducts',
            'stockMovementData'
        ));
    }
}
