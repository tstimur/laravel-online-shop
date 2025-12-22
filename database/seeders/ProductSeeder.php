<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id')->all();

        if ($categoryIds === []) {
            Product::factory(55)->create();

            return;
        }

        Product::factory(55)
            ->state(fn () => ['category_id' => fake()->randomElement($categoryIds)])
            ->create();
    }
}
