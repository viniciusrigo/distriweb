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
        Schema::create('caixas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id')->nullable();
            $table->decimal('valor_inicial', 10, 2)->nullable();
            $table->decimal('valor_final', 10, 2)->nullable();
            $table->decimal('valor_retirada', 10, 2)->nullable();
            $table->enum('status', ['a','f'])->default('a');
            $table->datetime('data_abertura')->nullable();
            $table->datetime('data_fechamento')->nullable();

            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caixas');
    }
};