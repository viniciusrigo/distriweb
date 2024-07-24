<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manifesto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "acao",
        "variavel_produto_id",
        "observacao",
        "quantidade",
        "data"
    ];
}
