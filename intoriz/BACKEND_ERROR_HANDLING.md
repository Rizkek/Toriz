# Backend Error Handling & Validation

## Overview
Implementasi error handling yang robust untuk backend dashboard dan stock operations.

---

## 1. Stock Service Error Handling

### File: `app/Services/StockService.php`

#### Current Implementation
```php
public function stockOut(Product $product, int $quantity, ...): StockTransaction
{
    if ($product->current_stock < $quantity) {
        throw new \Exception("Insufficient stock...");
    }
    // ... transaction logic
}
```

#### Recommended Enhancements

**A. Create Custom Exception Classes**
```php
// app/Exceptions/InsufficientStockException.php
class InsufficientStockException extends \Exception {
    public function __construct(Product $product, int $required)
    {
        $message = "Product '{$product->name}' has {$product->current_stock} units, "
                 . "but {$required} were requested";
        parent::__construct($message);
    }
}

// app/Exceptions/InvalidStockQuantityException.php
class InvalidStockQuantityException extends \Exception {
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
```

**B. Update StockService with Better Error Handling**
```php
use App\Exceptions\InsufficientStockException;

public function stockOut(Product $product, int $quantity, ...): StockTransaction
{
    // Validate product
    if (!$product || $product->trashed()) {
        throw new \InvalidArgumentException('Invalid product');
    }

    // Validate quantity
    if ($quantity <= 0) {
        throw new InvalidStockQuantityException(
            'Quantity must be greater than zero'
        );
    }

    // Check stock availability
    if ($product->current_stock < $quantity) {
        throw new InsufficientStockException($product, $quantity);
    }

    // Transaction logic...
    return DB::transaction(function () use (...) {
        try {
            // ... operations
        } catch (\Exception $e) {
            \Log::error('Stock operation failed', [
                'product_id' => $product->id,
                'operation' => 'stock_out',
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    });
}
```

---

## 2. Controller Error Handling

### File: `app/Http/Controllers/DashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Services\StockService;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(
        private StockService $stockService
    ) {}

    public function index()
    {
        try {
            // Fetch dashboard data
            $summary = $this->stockService->getStockSummary();

            if (!$summary) {
                Log::warning('Empty dashboard summary');
                $summary = $this->getDefaultSummary();
            }

            $lowStockProducts = Product::active()
                ->lowStock()
                ->with('category')
                ->limit(10)
                ->get()
                ->whenEmpty(function () {
                    Log::info('No low stock products found');
                });

            $recentTransactions = StockTransaction::with(['product', 'user'])
                ->latest('transaction_date')
                ->limit(10)
                ->get();

            $stockMovementData = $this->getStockMovementData();

            return view('dashboard', compact(
                'summary',
                'lowStockProducts',
                'recentTransactions',
                'stockMovementData'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard loading failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('dashboard', [
                'summary' => $this->getDefaultSummary(),
                'lowStockProducts' => collect(),
                'recentTransactions' => collect(),
                'stockMovementData' => collect(),
                'error' => 'Failed to load dashboard data'
            ]);
        }
    }

    private function getStockMovementData()
    {
        try {
            return StockTransaction::selectRaw(
                'DATE(transaction_date) as date, 
                 SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as stock_in,
                 SUM(CASE WHEN type = "out" THEN ABS(quantity) ELSE 0 END) as stock_out'
            )
            ->whereBetween('transaction_date', [
                now()->subDays(7),
                now()
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        } catch (\Exception $e) {
            Log::error('Stock movement query failed', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    private function getDefaultSummary(): array
    {
        return [
            'total_products' => 0,
            'total_stock_value' => 0,
            'low_stock_count' => 0,
            'out_of_stock_count' => 0,
        ];
    }
}
```

---

## 3. Validation Rules

### Stock Transaction Validation

**File: `app/Requests/StockInRequest.php`**
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100000',
            'reference_type' => 'nullable|string|max:50',
            'reference_id' => 'nullable|integer',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product must be selected',
            'product_id.exists' => 'Selected product not found',
            'quantity.required' => 'Quantity is required',
            'quantity.min' => 'Quantity must be at least 1',
            'quantity.max' => 'Quantity cannot exceed 100,000',
        ];
    }
}
```

**File: `app/Requests/StockOutRequest.php`**
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\SufficientStockRule;

class StockOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => [
                'required',
                'integer',
                'min:1',
                new SufficientStockRule($this->product_id),
            ],
            'reference_type' => 'nullable|string|max:50',
            'reference_id' => 'nullable|integer',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.min' => 'Cannot sell 0 or negative quantity',
            'quantity.required' => 'Quantity must be specified',
        ];
    }
}
```

**File: `app/Rules/SufficientStockRule.php`**
```php
<?php

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SufficientStockRule implements ValidationRule
{
    public function __construct(private int $productId) {}

    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void
    {
        $product = Product::find($this->productId);

        if (!$product || $product->current_stock < $value) {
            $available = $product?->current_stock ?? 0;
            $fail(
                "Insufficient stock. Available: $available, Requested: $value"
            );
        }
    }
}
```

---

## 4. Graceful Error Responses

### API Error Response Format

**File: `app/Http/Controllers/StockTransactionController.php`**
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockInRequest;
use App\Http\Requests\StockOutRequest;
use App\Models\Product;
use App\Services\StockService;
use App\Exceptions\InsufficientStockException;
use Illuminate\Http\JsonResponse;

class StockTransactionController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function stockIn(StockInRequest $request): JsonResponse
    {
        try {
            $product = Product::findOrFail($request->product_id);

            $transaction = $this->stockService->stockIn(
                $product,
                $request->quantity,
                $request->reference_type,
                $request->reference_id,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock added successfully',
                'data' => $transaction,
            ], 201);

        } catch (\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => 'PRODUCT_NOT_FOUND',
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Stock in operation failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add stock',
                'error' => 'STOCK_OPERATION_FAILED',
            ], 500);
        }
    }

    public function stockOut(StockOutRequest $request): JsonResponse
    {
        try {
            $product = Product::findOrFail($request->product_id);

            $transaction = $this->stockService->stockOut(
                $product,
                $request->quantity,
                $request->reference_type,
                $request->reference_id,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock removed successfully',
                'data' => $transaction,
            ], 201);

        } catch (InsufficientStockException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'INSUFFICIENT_STOCK',
            ], 422);

        } catch (\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => 'PRODUCT_NOT_FOUND',
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Stock out operation failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove stock',
                'error' => 'STOCK_OPERATION_FAILED',
            ], 500);
        }
    }
}
```

---

## 5. Logging & Monitoring

### Structured Logging

**File: `config/logging.php` (enhancement)**
```php
'channels' => [
    'stock' => [
        'driver' => 'single',
        'path' => storage_path('logs/stock.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
    'dashboard' => [
        'driver' => 'single',
        'path' => storage_path('logs/dashboard.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
],
```

**Usage in Service**
```php
use Illuminate\Support\Facades\Log;

class StockService
{
    public function stockIn(Product $product, int $quantity, ...): StockTransaction
    {
        Log::channel('stock')->info('Stock in transaction', [
            'product_id' => $product->id,
            'quantity' => $quantity,
            'user_id' => auth()->id(),
            'timestamp' => now(),
        ]);

        // ... operation
    }
}
```

---

## 6. Dashboard Error State

### Blade Template with Error Handling

**File: `resources/views/dashboard.blade.php`**
```blade
@if(isset($error))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <p class="text-red-700">{{ $error }}</p>
        </div>
    </div>
@endif
```

---

## 7. Health Check Endpoint (Bonus)

### Dashboard Health Check

**File: `app/Http/Controllers/HealthController.php`**
```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now(),
            'checks' => [],
        ];

        // Database check
        try {
            DB::connection()->getPdo();
            $health['checks']['database'] = 'ok';
        } catch (\Exception $e) {
            $health['checks']['database'] = 'failed';
            $health['status'] = 'unhealthy';
        }

        // Tables check
        try {
            $health['checks']['products'] = Product::count();
            $health['checks']['transactions'] = StockTransaction::count();
        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
        }

        return response()->json($health);
    }
}
```

**Route: `routes/web.php`**
```php
Route::get('/health', [HealthController::class, 'check']);
```

---

## Deployment Checklist

- [ ] Create custom exception classes
- [ ] Add validation rule for stock
- [ ] Implement error handling in controller
- [ ] Set up structured logging
- [ ] Add health check endpoint
- [ ] Test error scenarios
- [ ] Configure error responses
- [ ] Set up monitoring/alerts

---

**Status:** Recommended Implementation 📋
