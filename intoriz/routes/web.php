<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;

Route::redirect('/', '/inventory');

// Inventory Routes
Route::prefix('inventory')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/api/items', [InventoryController::class, 'getItems'])->name('inventory.getItems');
    Route::get('/api/categories', [InventoryController::class, 'getCategories'])->name('inventory.getCategories');
    Route::post('/api/items', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/api/items/{item}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/api/items/{item}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::post('/api/import', [InventoryController::class, 'importFile'])->name('inventory.import');
    Route::post('/api/upload-image', [InventoryController::class, 'uploadImage'])->name('inventory.uploadImage');
});
