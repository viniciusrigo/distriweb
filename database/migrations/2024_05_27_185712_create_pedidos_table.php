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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->float('total', 10, 2)->nullable();
            $table->float('lucro', 10, 2)->nullable();
            $table->unsignedBigInteger('forma_pagamento_id')->nullable();
            $table->integer('pontos')->nullable();
            $table->integer('pontos_troca')->nullable();
            $table->float('dinheiro', 10, 2)->nullable();
            $table->float('troco', 10, 2)->nullable();
            $table->float('frete', 10, 2)->nullable();
            $table->string('novo_endereco', 100)->nullable();
            $table->string('codigo', 4)->nullable();
            $table->enum('status', ['n','s', 'ac','e'])->nullable()->default('n');
            $table->datetime('data')->nullable();

            $table->foreign("user_id")->references("id")->on("users");
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
