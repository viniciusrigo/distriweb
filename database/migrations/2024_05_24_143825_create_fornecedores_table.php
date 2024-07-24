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
        Schema::create('fornecedores', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj', 15)->nullable();
            $table->string('nome', 100)->nullable();
            $table->string('fantasia', 50)->nullable();
            $table->string('logradouro', 100)->nullable();
            $table->integer('numero')->nullable();
            $table->string('municipio', 50)->nullable();
            $table->string('bairro', 50)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 10)->nullable();
            $table->enum('status', ['a', 'd'])->nullable()->default('a');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fornecedores');
    }
};
