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
        Schema::create('fluxo_bancos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('local_id');
            $table->decimal('valor', 8, 2);
            $table->enum('tipo', ['e', 's']);
            $table->datetime('data');

            $table->foreign('local_id')->references('id')->on('local_vendas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fluxo_bancos');
    }
};
