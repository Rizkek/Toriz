<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Products
Route::resource('products', ProductController::class);
Route::post('/products/lookup-barcode', [ProductController::class, 'lookupBarcode'])->name('products.lookup-barcode');

// Stock Operations
Route::prefix('stock')->name('stock.')->group(function () {
    Route::get('/transactions', [StockTransactionController::class, 'index'])->name('transactions');
    Route::post('/in', [StockTransactionController::class, 'stockIn'])->name('in');
    Route::post('/out', [StockTransactionController::class, 'stockOut'])->name('out');
    Route::post('/adjust', [StockTransactionController::class, 'adjust'])->name('adjust');
});

// Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/stock-movement', [ReportController::class, 'stockMovement'])->name('stock-movement');
    Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
    Route::get('/expiry', [ReportController::class, 'expiry'])->name('expiry');
    Route::get('/product-performance', [ReportController::class, 'productPerformance'])->name('product-performance');
    Route::get('/stock-value', [ReportController::class, 'stockValue'])->name('stock-value');
});
