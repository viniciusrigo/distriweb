<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutosComanda extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "comanda_id",
        "produto_id",
        "variavel_produto_id",
        "data"
    ];
}
