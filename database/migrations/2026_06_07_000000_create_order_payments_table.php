<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('provider', 50)->comment('Платежный провайдер');
            $table->string('status', 50)->comment('Статус платежа у провайдера');
            $table->decimal('amount', 12, 2)->comment('Сумма платежа');
            $table->string('currency', 3)->default('RUB')->comment('Валюта платежа');
            $table->string('external_payment_id')->nullable()->unique()->comment('ID платежа у провайдера');
            $table->uuid('idempotence_key')->nullable()->comment('Идемпотентный ключ запроса');
            $table->text('confirmation_url')->nullable()->comment('Ссылка на оплату');
            $table->json('request_payload')->nullable()->comment('Что отправили в провайдер');
            $table->json('response_payload')->nullable()->comment('Что вернул провайдер');
            $table->text('error_message')->nullable()->comment('Текст ошибки при обращении к API');
            $table->timestamp('paid_at')->nullable()->comment('Когда оплата подтверждена');
            $table->timestamp('canceled_at')->nullable()->comment('Когда платеж отменен');
            $table->timestamps();

            $table->index(['order_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
