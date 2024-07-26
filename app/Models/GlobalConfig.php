<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalConfig extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "cnpj",
        "razao_social",
        "nome_fantasia",
        "ie",
        "telefone",
        "codigo_interno",
        "minimo_produto",
        "cep",
        "logradouro",
        "numero",
        "complemento",
        "bairro",
        "localidade",
        "uf",
        "ibge",
        "ddd",
        "siafi",
    ];
}
