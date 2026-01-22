<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_dashboard_loads(): void
    {
        $response = $this->get('/inventory');

        $response->assertStatus(200);
        $response->assertViewIs('inventory.index');
    }

    public function test_get_items_returns_paginated_list(): void
    {
        // Create test items
        InventoryItem::factory()->count(20)->create();

        $response = $this->getJson('/inventory/api/items?page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'sku', 'name', 'quantity', 'unit_price']
                ],
                'current_page',
                'total',
                'per_page'
            ]);
    }

    public function test_get_items_with_search(): void
    {
        $item = InventoryItem::factory()->create(['sku' => 'TEST-SKU-001', 'name' => 'Test Product']);

        $response = $this->getJson('/inventory/api/items?search=TEST');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('TEST-SKU-001', $response->json('data.0.sku'));
    }

    public function test_add_item_with_validation(): void
    {
        $response = $this->postJson('/inventory/api/items', [
            'sku' => 'SKU-001',
            'name' => 'Test Item',
            'quantity' => 10,
            'unit_price' => 29.99,
            'category' => 'Electronics',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('inventory_items', ['sku' => 'SKU-001']);
    }

    public function test_add_item_with_invalid_sku_fails(): void
    {
        InventoryItem::factory()->create(['sku' => 'DUPLICATE']);

        $response = $this->postJson('/inventory/api/items', [
            'sku' => 'DUPLICATE',
            'name' => 'Test Item',
            'quantity' => 10,
            'unit_price' => 29.99,
        ]);

        $response->assertStatus(422);
    }

    public function test_update_item(): void
    {
        $item = InventoryItem::factory()->create(['name' => 'Old Name']);

        $response = $this->putJson("/inventory/api/items/{$item->id}", [
            'name' => 'New Name',
            'quantity' => 50,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'name' => 'New Name']);
    }

    public function test_delete_item(): void
    {
        $item = InventoryItem::factory()->create();

        $response = $this->deleteJson("/inventory/api/items/{$item->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('inventory_items', ['id' => $item->id]);
    }

    public function test_get_categories(): void
    {
        InventoryItem::factory()->create(['category' => 'Electronics']);
        InventoryItem::factory()->create(['category' => 'Tools']);
        InventoryItem::factory()->create(['category' => 'Electronics']);

        $response = $this->getJson('/inventory/api/categories');

        $response->assertStatus(200);
        $categories = $response->json();
        $this->assertContains('Electronics', $categories);
        $this->assertContains('Tools', $categories);
        $this->assertCount(2, $categories);
    }

    public function test_import_csv_file(): void
    {
        $csvContent = <<<CSV
sku,name,description,quantity,unit_price,category,location
SKU-001,Product A,Desc A,100,29.99,Electronics,Warehouse A
SKU-002,Product B,Desc B,50,49.99,Tools,Warehouse B
CSV;

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        $response = $this->postJson('/inventory/api/import', ['file' => $file]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_import_file_too_large_fails(): void
    {
        $file = \Illuminate\Http\UploadedFile::fake()->create('test.csv', 6000); // > 5MB

        $response = $this->postJson('/inventory/api/import', ['file' => $file]);

        $response->assertStatus(422);
    }

    public function test_upload_image(): void
    {
        $file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg', 100, 100);

        $response = $this->postJson('/inventory/api/upload-image', ['image' => $file]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['path', 'url']);
    }

    public function test_upload_invalid_image_type_fails(): void
    {
        $file = \Illuminate\Http\UploadedFile::fake()->create('test.txt', 100);

        $response = $this->postJson('/inventory/api/upload-image', ['image' => $file]);

        $response->assertStatus(422);
    }
}
