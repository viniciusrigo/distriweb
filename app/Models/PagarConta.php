<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagarConta extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "conta_id",
        "fornecedor_id",
        "vencimento",
        "valor",
        "status",
        "data_pagamento",
        "data_criacao",
    ];

}
