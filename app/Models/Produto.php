<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "nome",
        "categoria_id",
        "quantidade",
        "sku",
        "pontos",
        "preco",
        "preco_custo",
        "preco_promocao",
        "lucro",
        "desconto",
        "cfop",
        "ncm",
        "codigo_barras",
        "cst_csosn",
        "cst_pis",
        "cst_cofins",
        "cst_ipi",
        "perc_icms",
        "perc_pis",
        "perc_cofins",
        "perc_ipi",
        "promocao",
        "ativo",
        "validade",
        "ult_compra",
        "data_criacao"
    ];
}
