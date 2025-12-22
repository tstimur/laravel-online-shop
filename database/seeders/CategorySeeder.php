<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Смартфоны', 'slug' => 'smartphones'],
            ['name' => 'Ноутбуки', 'slug' => 'laptops'],
            ['name' => 'Аксессуары', 'slug' => 'accessories'],
            ['name' => 'Бытовая техника', 'slug' => 'home-appliances'],
            ['name' => 'Одежда', 'slug' => 'clothing'],
        ];

        foreach ($categories as $category) {
            Category::query()->firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']],
            );
        }
    }
}

