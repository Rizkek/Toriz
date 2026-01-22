<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    /**
     * Display inventory dashboard
     */
    public function index(): View
    {
        return view('inventory.index');
    }

    /**
     * Get paginated inventory items with filtering and search
     */
    public function getItems(Request $request): JsonResponse
    {
        $query = InventoryItem::query();

        // Search across multiple fields
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by quantity threshold
        if ($request->has('low_stock') && $request->low_stock) {
            $query->where('quantity', '<', 10);
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($items);
    }

    /**
     * Get unique categories for filter dropdown
     */
    public function getCategories(): JsonResponse
    {
        $categories = InventoryItem::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');

        return response()->json($categories);
    }

    /**
     * Store a single inventory item (manual entry)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:inventory_items,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
        ]);

        $item = InventoryItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item added successfully',
            'item' => $item,
        ], 201);
    }

    /**
     * Update an inventory item
     */
    public function update(Request $request, InventoryItem $item): JsonResponse
    {
        $validated = $request->validate([
            'sku' => 'sometimes|string|unique:inventory_items,sku,' . $item->id,
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'quantity' => 'sometimes|integer|min:0',
            'unit_price' => 'sometimes|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'item' => $item,
        ]);
    }

    /**
     * Delete an inventory item
     */
    public function destroy(InventoryItem $item): JsonResponse
    {
        // Delete associated image if exists
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully',
        ]);
    }

    /**
     * Upload and process CSV/Excel file
     */
    public function importFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:5120', // 5MB max
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('imports', 'local');

            // Read file and parse data
            $data = $this->parseImportFile($path);

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File is empty or invalid format',
                ], 422);
            }

            $imported = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                try {
                    // Skip if row is incomplete
                    if (!isset($row['sku']) || !isset($row['name']) || !isset($row['quantity'])) {
                        $errors[] = "Row " . ($index + 1) . ": Missing required fields (SKU, Name, Quantity)";
                        continue;
                    }

                    // Update or create
                    InventoryItem::updateOrCreate(
                        ['sku' => $row['sku']],
                        [
                            'name' => $row['name'] ?? '',
                            'description' => $row['description'] ?? null,
                            'quantity' => (int)($row['quantity'] ?? 0),
                            'unit_price' => (float)($row['unit_price'] ?? 0),
                            'category' => $row['category'] ?? null,
                            'location' => $row['location'] ?? null,
                        ]
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            // Clean up temp file
            Storage::disk('local')->delete($path);

            return response()->json([
                'success' => true,
                'message' => "Import completed: {$imported} items processed",
                'imported' => $imported,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload product image/photo
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,webp|max:2048', // 2MB max
            'item_id' => 'nullable|exists:inventory_items,id',
        ]);

        try {
            $image = $request->file('image');
            $path = $image->store('inventory-images', 'public');

            // If updating existing item
            if ($request->has('item_id')) {
                $item = InventoryItem::find($request->item_id);
                if ($item && $item->image_path) {
                    Storage::disk('public')->delete($item->image_path);
                }
                $item->update(['image_path' => $path]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Parse CSV and Excel files
     */
    private function parseImportFile(string $filePath): array
    {
        $fullPath = storage_path('app/' . $filePath);
        $data = [];

        if (str_ends_with($filePath, '.csv')) {
            if (($handle = fopen($fullPath, 'r')) !== false) {
                $headers = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== false) {
                    $data[] = array_combine($headers, $row);
                }
                fclose($handle);
            }
        } else {
            // For Excel files, use simple approach or integrate PhpSpreadsheet if needed
            // For now, we'll handle basic CSV operations
            if (function_exists('xlsx_to_array')) {
                $data = xlsx_to_array($fullPath);
            }
        }

        return $data;
    }
}
