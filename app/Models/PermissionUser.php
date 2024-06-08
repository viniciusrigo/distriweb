<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionUser extends Model
{
    use HasFactory;

    protected $table = 'permission_user';

    public $timestamps = false;

    protected $fillable = [
        "id",
        "user_id",
        "permission_id"
    ];
}
