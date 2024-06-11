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
        "produtos_id",
        "quantidade",
        "codigo_barras",
        "preco",
        "preco_custo",
        "preco_promocao",
        "data_cadastro"
    ];
}
