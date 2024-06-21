<?php

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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->decimal('lucro', 10, 2)->nullable();
            $table->unsignedBigInteger('forma_pagamentos_id')->nullable();
            $table->integer('pontos')->nullable();
            $table->decimal('dinheiro', 10, 2)->nullable();
            $table->decimal('troco', 10, 2)->nullable();
            $table->decimal('frete', 10, 2)->nullable();
            $table->string('novo_endereco', 100)->nullable();
            $table->enum('status', ['n','s', 'ac','e'])->nullable()->default('n');
            $table->datetime('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};