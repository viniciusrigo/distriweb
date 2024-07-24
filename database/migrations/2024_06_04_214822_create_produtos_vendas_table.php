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
        Schema::create('produtos_vendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venda_id')->nullable(false);
            $table->unsignedBigInteger('produto_id')->nullable(false);
            $table->unsignedBigInteger('variavel_produto_id')->nullable(false);
            $table->datetime('data_adicao')->nullable();

            $table->foreign('venda_id')->references('id')->on('vendas');
            $table->foreign('produto_id')->references('id')->on('produtos');
            $table->foreign('variavel_produto_id')->references('id')->on('variaveis_produtos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos_vendas');
    }
};
