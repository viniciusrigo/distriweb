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
        Schema::create('global_configs', function (Blueprint $table) {
            $table->id();
            $table->string("cnpj", 20)->nullable(false);
            $table->string("razao_social", 100)->nullable(false);
            $table->string("nome_fantasia", 50)->nullable(false);
            $table->string("ie", 20)->nullable();
            $table->string("telefone", 11)->nullable(false);
            $table->string("codigo_interno", 4)->nullable(false);
            $table->integer("minimo_produto")->nullable(false);
            $table->string("cep", 8)->nullable(false);
            $table->string("logradouro", 50)->nullable(false);
            $table->string("numero", 5)->nullable(false);
            $table->string("complemento", 50)->nullable();
            $table->string("bairro", 50)->nullable(false);
            $table->string("localidade", 50)->nullable(false);
            $table->string("uf", 2)->nullable(false);
            $table->string("ibge", 11)->nullable(false);
            $table->string("ddd", 3)->nullable(false);
            $table->string("siafi", 11)->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_configs');
    }
};
