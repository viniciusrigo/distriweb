<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "cpf_cliente",
        "local_id",
        "valor",
        "pontos",
        "lucro",
        "taxa",
        "comanda_id",
        "pedido_id",
        "estado",
        "forma_pagamento_id",
        "status",
        "chave",
        "numero_nfe",
        "data_venda"
    ];
}
