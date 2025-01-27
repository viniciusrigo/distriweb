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
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->string('cpf_cliente', 15)->nullable();
            $table->unsignedBigInteger('local_id')->nullable();
            $table->float('valor',10,2)->nullable();
            $table->float('lucro',10,2)->nullable();
            $table->float('taxa',4,2)->nullable();
            $table->integer('pontos')->nullable();
            $table->integer('descontos')->nullable();
            $table->unsignedBigInteger('comanda_id')->nullable();
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->enum('estado', ['a','e'])->default('a');
            $table->unsignedBigInteger('forma_pagamento_id')->nullable();
            $table->float('dinheiro',10,2)->nullable();
            $table->float('troco',10,2)->nullable();
            $table->enum('status', ['a','f'])->default('a');
            $table->string('chave')->nullable();
            $table->integer('numero_nfe')->nullable();
            $table->datetime('data_venda');


            $table->foreign('local_id')->references('id')->on('local_vendas');
            $table->foreign('comanda_id')->references('id')->on('comandas');
            $table->foreign('pedido_id')->references('id')->on('pedidos');
            $table->foreign('forma_pagamento_id')->references('id')->on('forma_pagamentos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
