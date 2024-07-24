<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "user_id",
        "valor_inicial",
        "valor_final",
        "valor_retirada",
        "data_abertura",
        "data_fechamento",
    ];
}
