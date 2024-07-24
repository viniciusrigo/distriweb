<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutosPedido extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "user_id",
        "pedido_id",
        "produto_id",
        "variavel_produto_id",
        "data"
    ];
}
