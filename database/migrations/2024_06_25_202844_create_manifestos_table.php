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
        Schema::create('manifestos', function (Blueprint $table) {
            $table->id();
            $table->string('acao', 15)->nullable();
            $table->unsignedBigInteger('variavel_produto_id')->nullable();
            $table->string('observacao', 100)->nullable();
            $table->integer('quantidade')->nullable();
            $table->dateTime('data');

            $table->foreign("variavel_produto_id")->references("id")->on("variaveis_produtos");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifestos');
    }
};
