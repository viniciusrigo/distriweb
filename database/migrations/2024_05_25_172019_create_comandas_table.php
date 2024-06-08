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
        Schema::create('comandas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 25)->nullable();
            $table->decimal('total', 8, 2)->nullable();
            $table->decimal('lucro', 8, 2)->nullable();
            $table->unsignedBigInteger('forma_pagamentos_id')->nullable();
            $table->decimal('dinheiro', 4, 2)->nullable();
            $table->decimal('troco', 4, 2)->nullable();
            $table->enum('status', ['a','f'])->default('a');
            $table->datetime('data_abertura')->nullable();
            $table->datetime('data_fechamento')->nullable();

            $table->foreign('forma_pagamentos_id')->references('id')->on('forma_pagamentos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comandas');
    }
};
