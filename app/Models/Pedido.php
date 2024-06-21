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
        "users_id",
        "total",
        "lucro",
        "forma_pagamentos_id",
        "pontos",
        "dinheiro",
        "troco",
        "frete",
        "novo_endereco",
        "status",
        "data",
    ];
}
