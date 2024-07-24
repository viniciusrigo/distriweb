<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comanda extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "nome",
        "comanda_produto_id",
        "total",
        "lucro",
        "forma_pagamento_id",
        "status",
        "data_abertura",
        "data_fechamento"
    ];

}
