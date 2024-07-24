<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fornecedor;
use Exception;
use Illuminate\Http\Request;

class FornecedorController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function page_index(){
        $fornecedores = Fornecedor::all();
        return view("site/admin/fornecedores/index", compact("fornecedores"));
    }

    public function new(Request $request){
        try{
            $dados = $request->except("_token");
            Fornecedor::create($dados);

            $success = "Fornecedor cadastrado com sucesso";
            session()->flash("success", $success);
            
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
}