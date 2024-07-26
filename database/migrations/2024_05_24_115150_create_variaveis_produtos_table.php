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
        Schema::create('variaveis_produtos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("produto_id");
            $table->string("variavel_nome", 50)->nullable();
            $table->integer("variavel_quantidade")->nullable();
            $table->boolean("fardo_quantidade")->nullable()->default(false);
            $table->integer('pontos')->nullable()->default(null);
            $table->float('preco', 8, 2)->nullable(false)->default(0);
            $table->float('preco_custo', 8, 2)->nullable(false)->default(0);
            $table->float('preco_promocao', 8, 2)->nullable()->default(0);
            $table->float('lucro', 8, 2)->nullable();
            $table->string('codigo_barras', 20)->unique()->nullable();
            $table->date('validade')->nullable();
            $table->enum('promocao', ['n', 's'])->nullable()->default('n');
            $table->enum('variavel_ativo', ['n', 's'])->nullable(false)->default('s');
            $table->date('ult_compra')->nullable();
            $table->date('data_cadastro')->default(now());

            $table->foreign("produto_id")->references("id")->on("produtos");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variaveis_produtos');
    }
};
