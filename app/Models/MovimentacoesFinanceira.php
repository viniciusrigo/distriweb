<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimentacoesFinanceira extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "local_id",
        "cliente_fornecedor",
        "valor",
        "lucro",
        "tipo",
        "data"
    ];

}
