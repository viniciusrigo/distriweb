<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariaveisProduto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "produto_id",
        "variavel_nome",
        "variavel_quantidade",
        "fardo_quantidade",
        "pontos",
        "preco",
        "preco_custo",
        "preco_promocao",
        "lucro",
        "codigo_barras",
        "validade",
        "promocao",
        "variavel_ativo",
        "ult_compra",
        "data_cadastro"
    ];
}
