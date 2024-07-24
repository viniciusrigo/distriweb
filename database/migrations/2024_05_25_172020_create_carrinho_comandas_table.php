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
        Schema::create('carrinho_comandas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comanda_id')->nullable(false);
            $table->unsignedBigInteger('produto_id')->nullable(false);
            $table->unsignedBigInteger('variavel_produto_id')->nullable();
            $table->datetime('data')->nullable();

            $table->foreign('comanda_id')->references('id')->on('comandas');
            $table->foreign('produto_id')->references('id')->on('produtos');
            $table->foreign('variavel_produto_id')->references('id')->on('variaveis_produtos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrinho_comandas');
    }
};
