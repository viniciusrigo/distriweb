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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('cliente', ['s','n'])->default('n');
            $table->string('cpf', 11)->unique()->nullable();
            $table->integer('pontos')->nullable();
            $table->string('celular', 15)->unique()->nullable();
            $table->string('cep', 11)->nullable();
            $table->string('zona', 10)->nullable();
            $table->string('logradouro', 100)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('localidade', 100)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('ibge', 15)->nullable();
            $table->string('ddd', 3)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
