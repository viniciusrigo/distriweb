<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use Illuminate\Http\Request;

class BancoController extends Controller
{
    public function index(){
        $bancos = Banco::all();
        return view('site/admin/bancos/index', compact('bancos'));
    }

}
