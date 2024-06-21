<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoConta extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "tipo_conta"
    ];
}
