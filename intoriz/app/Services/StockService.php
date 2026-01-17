<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Record stock in transaction (purchase/production)
     */
    public function stockIn(Product $product, int $quantity, string $referenceType = null, int $referenceId = null, string $notes = null): StockTransaction
    {
        return DB::transaction(function () use ($product, $quantity, $referenceType, $referenceId, $notes) {
            $beforeQty = $product->current_stock;
            $afterQty = $beforeQty + $quantity;

            // Update product stock
            $product->update(['current_stock' => $afterQty]);

            // Create stock transaction record
            return StockTransaction::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $quantity,
                'before_qty' => $beforeQty,
                'after_qty' => $afterQty,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'user_id' => auth()->id() ?? null,
                'transaction_date' => now(),
            ]);
        });
    }

    /**
     * Record stock out transaction (sale/usage)
     */
    public function stockOut(Product $product, int $quantity, string $referenceType = null, int $referenceId = null, string $notes = null): StockTransaction
    {
        if ($product->current_stock < $quantity) {
            throw new \Exception("Insufficient stock. Available: {$product->current_stock}, Required: {$quantity}");
        }

        return DB::transaction(function () use ($product, $quantity, $referenceType, $referenceId, $notes) {
            $beforeQty = $product->current_stock;
            $afterQty = $beforeQty - $quantity;

            // Update product stock
            $product->update(['current_stock' => $afterQty]);

            // Create stock transaction record
            return StockTransaction::create([
                'product_id' => $product->id,
                'type' => 'out',
                'quantity' => -$quantity, // Negative for out
                'before_qty' => $beforeQty,
                'after_qty' => $afterQty,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'user_id' => auth()->id() ?? null,
                'transaction_date' => now(),
            ]);
        });
    }

    /**
     * Manual stock adjustment
     */
    public function adjustStock(Product $product, int $newQuantity, string $notes = null): StockTransaction
    {
        return DB::transaction(function () use ($product, $newQuantity, $notes) {
            $beforeQty = $product->current_stock;
            $difference = $newQuantity - $beforeQty;

            // Update product stock
            $product->update(['current_stock' => $newQuantity]);

            // Create stock transaction record
            return StockTransaction::create([
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity' => $difference,
                'before_qty' => $beforeQty,
                'after_qty' => $newQuantity,
                'reference_type' => 'Manual',
                'notes' => $notes,
                'user_id' => auth()->id() ?? null,
                'transaction_date' => now(),
            ]);
        });
    }

    /**
     * Get stock summary
     */
    public function getStockSummary(): array
    {
        return [
            'total_products' => Product::active()->count(),
            'total_stock_value' => Product::active()->sum(DB::raw('current_stock * cost_price')),
            'low_stock_count' => Product::active()->lowStock()->count(),
            'out_of_stock_count' => Product::active()->where('current_stock', 0)->count(),
        ];
    }
}
