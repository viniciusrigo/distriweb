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
        Schema::create('pagar_contas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conta_id')->nullable(false);
            $table->unsignedBigInteger('fornecedor_id')->nullable(false);
            $table->unsignedBigInteger('banco_id')->nullable(false);
            $table->date('vencimento')->nullable(false);
            $table->float('valor',8,2)->nullable(false);
            $table->enum('status', ['a','p'])->default('a');
            $table->datetime('data_pagamento')->nullable();
            $table->date('data_criacao');

            $table->foreign('conta_id')->references('id')->on('tipo_contas');
            $table->foreign('fornecedor_id')->references('id')->on('fornecedores');
            $table->foreign('banco_id')->references('id')->on('bancos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagar_contas');
    }
};
