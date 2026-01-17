<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(
        private StockService $stockService
    ) {
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('barcode', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            })
            ->when($request->category_id, function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            ->when($request->status, function ($q) use ($request) {
                if ($request->status === 'low_stock') {
                    $q->lowStock();
                } elseif ($request->status === 'out_of_stock') {
                    $q->where('current_stock', 0);
                }
            });

        $products = $query->latest()->paginate(20);
        $categories = Category::active()->get();
        $suppliers = Supplier::active()->get();

        return view('products.index', compact('products', 'categories', 'suppliers'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $suppliers = Supplier::active()->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'barcode' => 'nullable|unique:products,barcode',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'images.*' => 'nullable|image|max:2048',
            'initial_stock' => 'nullable|integer|min:0',
        ]);

        // Generate SKU if not provided
        $validated['sku'] = 'PRD-' . strtoupper(Str::random(8));

        // Handle image uploads
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = Storage::url($path);
            }
            $validated['images'] = $images;
        }

        $product = Product::create($validated);

        // If initial stock provided, record stock in transaction
        if ($request->filled('initial_stock') && $request->initial_stock > 0) {
            $this->stockService->stockIn(
                $product,
                $request->initial_stock,
                'Manual',
                null,
                'Initial stock'
            );
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'barcode' => 'nullable|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'images.*' => 'nullable|image|max:2048',
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $images = $product->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = Storage::url($path);
            }
            $validated['images'] = $images;
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }

    /**
     * Barcode lookup API
     */
    public function lookupBarcode(Request $request)
    {
        $product = Product::where('barcode', $request->barcode)->first();

        return response()->json($product ? [
            'found' => true,
            'product' => $product->load(['category', 'supplier'])
        ] : [
            'found' => false
        ]);
    }
}
