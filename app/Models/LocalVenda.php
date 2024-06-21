<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalVenda extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "nome",
        "credito_id",
        "debito_id",
    ];
}
