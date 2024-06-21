<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutosComanda extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "comandas_id",
        "produtos_id",
        "data_adicao"
    ];
}
