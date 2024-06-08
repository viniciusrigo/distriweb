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
        "local",
        "valor",
        "pontos",
        "lucro",
        "comandas_id",
        "estado",
        "forma_pagamentos_id",
        "status",
        "chave",
        "numero_nfe",
        "data_venda"
    ];
}
