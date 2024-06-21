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
            $table->integer('quantidade')->nullable(false)->default(0);
            $table->unsignedBigInteger('categoria_produtos_id')->nullable(false);
            $table->unsignedBigInteger('subcategoria_produtos_id')->nullable();
            $table->string('sku')->nullable()->default(null);
            $table->integer('pontos')->nullable()->default(null);
            $table->decimal('preco', 8, 2)->nullable(false)->default(0);
            $table->decimal('preco_custo', 8, 2)->nullable(false)->default(0);
            $table->decimal('preco_promocao', 8, 2)->nullable(false)->default(0);
            $table->decimal('desconto', 8, 2)->nullable();
            $table->decimal('lucro', 8, 2)->nullable();
            $table->integer('cfop')->nullable();
            $table->string('ncm', 20)->nullable();
            $table->string('codigo_barras', 20)->unique()->nullable(false);
            $table->string('cst_csosn')->nullable();
            $table->string('cst_pis')->nullable();
            $table->string('cst_cofins')->nullable();
            $table->string('cst_ipi')->nullable();
            $table->decimal('perc_icms')->nullable();
            $table->decimal('perc_pis')->nullable();
            $table->decimal('perc_cofins')->nullable();
            $table->decimal('perc_ipi')->nullable();
            $table->enum('promocao', ['n', 's'])->nullable()->default('n');
            $table->enum('ativo', ['n', 's'])->nullable(false)->default('s');
            $table->date('validade')->nullable();
            $table->date('ult_compra')->nullable();
            $table->date('data_cadastro')->default(now());

            $table->foreign('categoria_produtos_id')->references('id')->on('categoria_produtos');
            $table->foreign('subcategoria_produtos_id')->references('id')->on('subcategoria_produtos');
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
