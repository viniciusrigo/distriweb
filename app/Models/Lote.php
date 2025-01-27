<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "produto_id",
        "variavel_produto_id",
        "quantidade",
        "codigo_barras",
        "preco",
        "preco_custo",
        "preco_promocao",
        "validade",
        "data_cadastro"
    ];
}
