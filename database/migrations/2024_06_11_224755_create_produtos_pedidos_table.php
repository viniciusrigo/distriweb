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
        Schema::create('produtos_pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id')->nullable();
            $table->unsignedBigInteger('pedidos_id')->nullable();
            $table->unsignedBigInteger('produtos_id')->nullable();
            $table->unsignedBigInteger('qtd')->nullable();
            $table->datetime('data')->nullable();

            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('produtos_id')->references('id')->on('produtos');
            $table->foreign('pedidos_id')->references('id')->on('pedidos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos_pedidos');
    }
};
