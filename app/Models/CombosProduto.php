<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CombosProduto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "produto_id",
        "variavel_produto_id",
        "combo_quantidade"
    ];
}
