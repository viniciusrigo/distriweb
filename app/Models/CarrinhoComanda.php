<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrinhoComanda extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "comanda_id",
        "estoques_id",
        "pnc",
        "valor",
        "data_compra"
    ];
}
