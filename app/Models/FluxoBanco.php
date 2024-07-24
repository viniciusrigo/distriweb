<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FluxoBanco extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id",
        "local",
        "valor",
        "tipo",
        "motivo",
        "data"
    ];
}
