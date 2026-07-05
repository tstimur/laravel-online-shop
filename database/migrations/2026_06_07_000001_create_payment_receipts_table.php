<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_payment_id')->constrained()->onDelete('cascade');
            $table->string('provider', 50)->comment('Провайдер чека');
            $table->string('type', 50)->default('payment')->comment('Тип чека');
            $table->string('status', 50)->comment('Статус чека');
            $table->string('external_receipt_id')->nullable()->unique()->comment('ID чека у провайдера');
            $table->boolean('send_to_customer')->default(true)->comment('Нужно ли отправить чек покупателю');
            $table->json('request_payload')->nullable()->comment('Что отправили в API чеков');
            $table->json('response_payload')->nullable()->comment('Что вернул API чеков');
            $table->text('error_message')->nullable()->comment('Текст ошибки при регистрации чека');
            $table->timestamps();

            $table->index(['order_payment_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
