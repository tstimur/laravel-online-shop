<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table
                ->id()
                ->comment('Первичный ключ товара');
            $table
                ->string('name')
                ->comment('Название товара');
            $table
                ->text('description')
                ->nullable()
                ->comment('Описание товара');
            $table
                ->decimal('price', 10, 2)
                ->comment('Цена товара');
            $table
                ->unsignedInteger('stock')
                ->default(0)
                ->comment('Количество на складе');
            $table
                ->string('sku')
                ->unique()
                ->comment('Артикул товара');
            $table
                ->string('image')
                ->nullable()
                ->comment('Путь к изображению товара');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
