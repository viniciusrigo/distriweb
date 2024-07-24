<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrinhoVenda extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "local_id",
        "venda_id",
        "produto_id",
        "variavel_produto_id",
        "data_adicao"
    ];
}
