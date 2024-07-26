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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 40)->nullable(false);
            $table->unsignedBigInteger('categoria_id')->nullable(false);
            $table->integer('variavel_produto_id')->nullable();
            $table->string('sku')->nullable()->default(null);
            $table->integer('cfop')->nullable();
            $table->string('ncm', 20)->nullable();
            $table->string('cst_csosn')->nullable();
            $table->string('cst_pis')->nullable();
            $table->string('cst_cofins')->nullable();
            $table->string('cst_ipi')->nullable();
            $table->float('perc_icms')->nullable();
            $table->float('perc_pis')->nullable();
            $table->float('perc_cofins')->nullable();
            $table->float('perc_ipi')->nullable();
            $table->enum('ativo', ['n', 's'])->nullable(false)->default('s');

            $table->foreign('categoria_id')->references('id')->on('categoria_produtos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
