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
        Schema::create('fluxo_caixas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caixas_id')->nullable(false);
            $table->decimal('venda',10,2)->nullable();
            $table->decimal('dinheiro',10,2)->nullable();
            $table->decimal('troco',10,2)->nullable();
            $table->datetime('data')->nullable();

            $table->foreign('caixas_id')->references('id')->on('caixas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fluxo_caixas');
    }
};
