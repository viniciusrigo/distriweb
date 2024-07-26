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
        Schema::create('movimentacoes_financeiras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('local_id');
            $table->string('cliente_fornecedor', 30);
            $table->float('valor', 8, 2);
            $table->float('lucro', 8, 2)->nullable();
            $table->unsignedBigInteger('forma_pagamento_id')->nullable();
            $table->enum('tipo', ['e', 's']);
            $table->datetime('data');

            $table->foreign('forma_pagamento_id')->references('id')->on('forma_pagamentos');
            $table->foreign('local_id')->references('id')->on('local_vendas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_financeiras');
    }
};
