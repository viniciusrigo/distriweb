<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "user_id",
        "total",
        "lucro",
        "forma_pagamento_id",
        "pontos",
        "pontos_troca",
        "dinheiro",
        "troco",
        "frete",
        "novo_endereco",
        "codigo",
        "status",
        "data",
    ];
}
