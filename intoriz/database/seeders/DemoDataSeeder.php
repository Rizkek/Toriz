<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Categories
        $categories = [
            ['name' => 'Rokok', 'slug' => 'rokok', 'icon' => 'smoking_rooms'],
            ['name' => 'Sembako', 'slug' => 'sembako', 'icon' => 'shopping_basket'],
            ['name' => 'Cemilan', 'slug' => 'cemilan', 'icon' => 'cookie'],
            ['name' => 'Minuman', 'slug' => 'minuman', 'icon' => 'local_drink'],
            ['name' => 'Peralatan', 'slug' => 'peralatan', 'icon' => 'construction'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Suppliers
        $suppliers = [
            [
                'name' => 'PT Gudang Garam',
                'contact_person' => 'Budi Santoso',
                'phone' => '021 - 5555-1234',
                'address' => 'Jakarta Pusat',
            ],
            [
                'name' => 'CV Sembako Jaya',
                'contact_person' => 'Siti Aminah',
                'phone' => '021-5555-5678',
                'address' => 'Tangerang',
            ],
            [
                'name' => 'UD Snack Mantap',
                'contact_person' => 'Ahmad Yani',
                'phone' => '021-5555-9012',
                'address' => 'Bekasi',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        // Create Sample Products
        $products = [
            // Rokok
            ['name' => 'Gudang Garam Signature', 'category_id' => 1, 'supplier_id' => 1, 'price' => 25000, 'cost_price' => 20000, 'min_stock' => 50],
            ['name' => 'Sampoerna Mild', 'category_id' => 1, 'supplier_id' => 1, 'price' => 28000, 'cost_price' => 23000, 'min_stock' => 40],
            ['name' => 'Djarum Super', 'category_id' => 1, 'supplier_id' => 1, 'price' => 24000, 'cost_price' => 19500, 'min_stock' => 30],

            // Sembako
            ['name' => 'Beras Premium 5kg', 'category_id' => 2, 'supplier_id' => 2, 'price' => 75000, 'cost_price' => 65000, 'min_stock' => 20, 'unit' => 'kg'],
            ['name' => 'Minyak Goreng 2L', 'category_id' => 2, 'supplier_id' => 2, 'price' => 35000, 'cost_price' => 30000, 'min_stock' => 25, 'unit' => 'liter'],
            ['name' => 'Gula Pasir 1kg', 'category_id' => 2, 'supplier_id' => 2, 'price' => 15000, 'cost_price' => 12500, 'min_stock' => 30],
            ['name' => 'Telur 1kg', 'category_id' => 2, 'supplier_id' => 2, 'price' => 28000, 'cost_price' => 24000, 'min_stock' => 15],

            // Cemilan
            ['name' => 'Chitato All Variant', 'category_id' => 3, 'supplier_id' => 3, 'price' => 12000, 'cost_price' => 9500, 'min_stock' => 40],
            ['name' => 'Taro Net', 'category_id' => 3, 'supplier_id' => 3, 'price' => 8000, 'cost_price' => 6500, 'min_stock' => 50],
            ['name' => 'Oreo Original', 'category_id' => 3, 'supplier_id' => 3, 'price' => 10000, 'cost_price' => 8000, 'min_stock' => 35],

            // Minuman
            ['name' => 'Coca Cola 1.5L', 'category_id' => 4, 'supplier_id' => 2, 'price' => 12000, 'cost_price' => 9500, 'min_stock' => 30, 'unit' => 'botol'],
            ['name' => 'Aqua 600ml', 'category_id' => 4, 'supplier_id' => 2, 'price' => 4000, 'cost_price' => 3000, 'min_stock' => 100, 'unit' => 'botol'],
            ['name' => 'Teh Pucuk Harum 350ml', 'category_id' => 4, 'supplier_id' => 2, 'price' => 5000, 'cost_price' => 3800, 'min_stock' => 80, 'unit' => 'botol'],
        ];

        $stockService = new StockService();

        foreach ($products as $productData) {
            $productData['sku'] = 'PRD-' . strtoupper(Str::random(6));
            $productData['barcode'] = rand(1000000000000, 9999999999999);
            $productData['unit'] = $productData['unit'] ?? 'pcs';

            $product = Product::create($productData);

            // Add initial stock
            $initialStock = rand(50, 200);
            $stockService->stockIn($product, $initialStock, 'Initial', null, 'Starting inventory');
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Created: ' . count($categories) . ' categories');
        $this->command->info('Created: ' . count($suppliers) . ' suppliers');
        $this->command->info('Created: ' . count($products) . ' products');
    }
}
