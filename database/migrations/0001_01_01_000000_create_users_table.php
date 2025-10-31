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
        Schema::create('users', function (Blueprint $table) {
            $table->id()
                ->comment('Первичный ключ пользователя');
            $table->string('first_name')
                ->comment('Имя пользователя');
            $table->string('last_name')
                ->comment('Фамилия пользователя');
            $table->string('email')
                ->unique()
                ->comment('Уникальный адрес электронной почты');
            $table->timestamp('email_verified_at')
                ->nullable();
            $table->string('password');
            $table->string('phone', 20)
                ->nullable()
                ->unique()
                ->comment('Номер телефона пользователя');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
