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
        Schema::create('forma_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 25);
            $table->decimal('taxa', 4, 2)->nullable();
            $table->unsignedBigInteger('banco_id')->nullable();

            $table->foreign('banco_id')->references('id')->on('bancos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forma_pagamentos');
    }
};
