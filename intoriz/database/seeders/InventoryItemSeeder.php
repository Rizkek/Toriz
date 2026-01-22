<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'sku' => 'SKU-001',
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with USB receiver',
                'quantity' => 150,
                'unit_price' => 24.99,
                'category' => 'Electronics',
                'location' => 'Warehouse A',
            ],
            [
                'sku' => 'SKU-002',
                'name' => 'USB-C Hub',
                'description' => '7-port USB-C hub with power delivery',
                'quantity' => 75,
                'unit_price' => 49.99,
                'category' => 'Electronics',
                'location' => 'Warehouse A',
            ],
            [
                'sku' => 'SKU-003',
                'name' => 'Laptop Stand',
                'description' => 'Adjustable aluminum laptop stand',
                'quantity' => 8,
                'unit_price' => 34.99,
                'category' => 'Accessories',
                'location' => 'Warehouse B',
            ],
            [
                'sku' => 'SKU-004',
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB mechanical gaming keyboard',
                'quantity' => 120,
                'unit_price' => 89.99,
                'category' => 'Electronics',
                'location' => 'Warehouse A',
            ],
            [
                'sku' => 'SKU-005',
                'name' => 'Monitor Arm',
                'description' => 'Dual monitor mount VESA compatible',
                'quantity' => 3,
                'unit_price' => 79.99,
                'category' => 'Accessories',
                'location' => 'Warehouse B',
            ],
            [
                'sku' => 'SKU-006',
                'name' => 'Desk Lamp',
                'description' => 'LED desk lamp with USB charging',
                'quantity' => 300,
                'unit_price' => 39.99,
                'category' => 'Lighting',
                'location' => 'Warehouse C',
            ],
            [
                'sku' => 'SKU-007',
                'name' => 'Cable Organizer',
                'description' => 'Desktop cable management system',
                'quantity' => 500,
                'unit_price' => 9.99,
                'category' => 'Accessories',
                'location' => 'Warehouse B',
            ],
            [
                'sku' => 'SKU-008',
                'name' => 'Phone Stand',
                'description' => 'Premium phone stand for desk',
                'quantity' => 250,
                'unit_price' => 15.99,
                'category' => 'Accessories',
                'location' => 'Warehouse B',
            ],
            [
                'sku' => 'SKU-009',
                'name' => 'HDMI Cable',
                'description' => 'High-speed HDMI 2.1 cable 6ft',
                'quantity' => 400,
                'unit_price' => 12.99,
                'category' => 'Cables',
                'location' => 'Warehouse C',
            ],
            [
                'sku' => 'SKU-010',
                'name' => 'Power Bank',
                'description' => '20000mAh portable charger with fast charging',
                'quantity' => 9,
                'unit_price' => 34.99,
                'category' => 'Electronics',
                'location' => 'Warehouse A',
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::updateOrCreate(
                ['sku' => $item['sku']],
                $item
            );
        }
    }
}
