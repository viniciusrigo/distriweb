<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function page_index() {
        $clientes = Cliente::where("cliente", "s")->get()->toArray();
        for ($i = 0; $i < count($clientes); $i++) {
            $ddd = substr($clientes[0]["celular"], 0, 2);
            $p1 = substr($clientes[0]["celular"], 2, 5);
            $p2 = substr($clientes[0]["celular"], 7, 11);
            $clientes[0]["celular"] = "(".$ddd.") ".$p1."-".$p2;
        }

        return view("site/admin/clientes/index", compact("clientes"));
    }
}
