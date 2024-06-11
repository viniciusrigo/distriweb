<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FluxoCaixa extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "caixas_id",
        "dinheiro",
        "troco",
        "data"
    ];
}
