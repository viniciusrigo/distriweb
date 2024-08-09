<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "fornecedores";

    protected $fillable = [
        "id",
        "cnpj",
        "contato",
        "nome",
        "fantasia",
        "logradouro",
        "numero",
        "municipio",
        "bairro",
        "uf",
        "cep",
        "status",
    ];
}
