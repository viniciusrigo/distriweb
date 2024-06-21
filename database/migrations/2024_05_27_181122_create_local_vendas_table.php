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
        Schema::create('local_vendas', function (Blueprint $table) {
            $table->id();
            $table->string('local', 15)->nullable();
            $table->unsignedBigInteger('credito_id')->nullable();
            $table->unsignedBigInteger('debito_id')->nullable();

            $table->foreign('credito_id')->references('id')->on('forma_pagamentos');
            $table->foreign('debito_id')->references('id')->on('forma_pagamentos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_vendas');
    }
};
