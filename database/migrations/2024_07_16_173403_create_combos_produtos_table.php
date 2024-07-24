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
        Schema::create('combos_produtos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("produto_id");
            $table->unsignedBigInteger("variavel_produto_id");
            $table->unsignedBigInteger("combo_quantidade");

            $table->foreign("produto_id")->references("id")->on("produtos");
            $table->foreign("variavel_produto_id")->references("id")->on("variaveis_produtos");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combos_produtos');
    }
};
