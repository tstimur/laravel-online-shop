<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucfirst(fake()->words(3, true));

        return [
            'name' => $name,
            'description' => fake()->sentence(12),
            'price' => fake()->randomFloat(2, 500, 50000),
            'stock' => fake()->numberBetween(0, 100),
            'sku' => strtoupper('SKU-' . Str::random(8)),
            'image' => fake()->boolean(40)
                ? null
                : 'products/sample-' . fake()->numberBetween(1, 5) . '.jpg',
        ];
    }
}
