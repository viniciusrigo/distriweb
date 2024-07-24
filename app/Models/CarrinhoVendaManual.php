<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrinhoVendaManual extends Model
{
    use HasFactory;

    protected $table = "carrinho_venda_manual";

    public $timestamps = false;

    protected $fillable = [
        "id",
        "venda_id",
        "produto_id",
        "variavel_produto_id",
        "data_adicao"
    ];
}
