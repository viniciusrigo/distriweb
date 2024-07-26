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
            $table->unsignedBigInteger('banco_id');
            $table->float('valor', 8, 2);
            $table->enum('tipo', ['e', 's']);
            $table->enum('mov_extra', ['n', 's'])->nullable()->default('n');
            $table->string('motivo', 30)->nullable();
            $table->datetime('data');

            $table->foreign('banco_id')->references('id')->on('bancos');
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
