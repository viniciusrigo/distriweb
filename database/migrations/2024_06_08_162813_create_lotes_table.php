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
            $table->unsignedBigInteger('produtos_id');
            $table->integer('quantidade')->nullable();
            $table->string('codigo_barras', 30)->nullable();
            $table->decimal('preco', 10, 2)->nullable();
            $table->decimal('preco_custo', 10, 2)->nullable();
            $table->decimal('preco_promocao', 10, 2)->nullable();
            $table->datetime('data_cadastro');

            $table->foreign('produtos_id')->references('id')->on('produtos');
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
