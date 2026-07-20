<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('daily_sales_reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date')->unique()->comment('Дата отчёта');
            $table->unsignedInteger('total_orders')->default(0)->comment('Всего созданных заказов');
            $table->unsignedInteger('successful_orders')->default(0)->comment('Успешные продажи');
            $table->decimal('revenue', 12, 2)->default(0)->comment('Выручка');
            $table->unsignedInteger('canceled_orders')->default(0)->comment('Отменённые заказы');
            $table->timestamp('generated_at')->comment('Когда отчёт пересчитан');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_sales_reports');
    }
};
