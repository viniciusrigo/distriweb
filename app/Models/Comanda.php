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
        "comanda_produtos_id",
        "total",
        "lucro",
        "forma_pagamentos_id",
        "status",
        "data_abertura",
        "data_fechamento"
    ];

}
