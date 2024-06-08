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
            $table->unsignedBigInteger('comandas_id')->nullable(false);
            $table->unsignedBigInteger('produtos_id')->nullable(false);
            $table->datetime('data_compra');

            $table->foreign('comandas_id')->references('id')->on('comandas');
            $table->foreign('produtos_id')->references('id')->on('produtos');
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
