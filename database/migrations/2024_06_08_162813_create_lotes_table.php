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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produto_id');
            $table->unsignedBigInteger('variavel_produto_id');
            $table->integer('quantidade')->nullable();
            $table->string('codigo_barras', 30)->nullable();
            $table->float('preco', 10, 2)->nullable();
            $table->float('preco_custo', 10, 2)->nullable();
            $table->float('preco_promocao', 10, 2)->nullable();
            $table->datetime('validade');
            $table->datetime('data_cadastro');

            $table->foreign('produto_id')->references('id')->on('produtos');
            $table->foreign('variavel_produto_id')->references('id')->on('variaveis_produtos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
