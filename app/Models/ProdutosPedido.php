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
        "users_id",
        "pedidos_id",
        "produtos_id",
        "qtd",
        "data"
    ];
}
