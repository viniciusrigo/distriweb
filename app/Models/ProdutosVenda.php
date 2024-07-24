<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutosVenda extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "venda_id",
        "produto_id",
        "variavel_produto_id",
        "data_adicao"
    ];
}
