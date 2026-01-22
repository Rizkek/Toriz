<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    /**
     * The name of the model that this factory creates.
     *
     * @var class-string<\App\Models\InventoryItem>
     */
    protected $model = InventoryItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => $this->faker->unique()->bothify('SKU-????-####'),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'quantity' => $this->faker->numberBetween(0, 1000),
            'unit_price' => $this->faker->numberBetween(1000, 100000) / 100,
            'category' => $this->faker->randomElement(['Electronics', 'Tools', 'Accessories', 'Cables', 'Storage']),
            'location' => $this->faker->randomElement(['Warehouse A', 'Warehouse B', 'Warehouse C']),
        ];
    }

    /**
     * Mark item as low stock
     */
    public function lowStock(): Factory
    {
        return $this->state(function () {
            return [
                'quantity' => $this->faker->numberBetween(0, 9),
            ];
        });
    }

    /**
     * Mark item as out of stock
     */
    public function outOfStock(): Factory
    {
        return $this->state(function () {
            return [
                'quantity' => 0,
            ];
        });
    }
}
