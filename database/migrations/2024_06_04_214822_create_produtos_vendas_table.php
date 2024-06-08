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
            $table->unsignedBigInteger('vendas_id')->nullable(false);
            $table->unsignedBigInteger('produtos_id')->nullable(false);
            $table->datetime('data_adicao')->nullable();

            $table->foreign('vendas_id')->references('id')->on('vendas');
            $table->foreign('produtos_id')->references('id')->on('produtos');
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
