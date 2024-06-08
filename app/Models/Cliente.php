<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "users";

    protected $fillable = [
        'id',
        'name',
        'email',
        'cliente',
        'cpf',
        'pontos',
        'celular',
        'cep',
        'zona',
        'logradouro',
        'complemento',
        'bairro',
        'localidade',
        'uf',
        'ibge',
        'ddd',
    ];

}
