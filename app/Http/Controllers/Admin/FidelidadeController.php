<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VariaveisProduto;
use Exception;

class FidelidadeController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }
    
    public function page_index(){
        $ativados = VariaveisProduto::join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.variavel_nome", "variaveis_produtos.produto_id", "variaveis_produtos.id", "variaveis_produtos.pontos")
        ->where("variavel_ativo", "s")->where("pontos", ">", 0)->get();

        $desativados = VariaveisProduto::join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.variavel_nome", "variaveis_produtos.produto_id", "variaveis_produtos.id", "variaveis_produtos.pontos")
        ->where("variavel_ativo", "s")
        ->where("pontos", 0)
        ->where("promocao", "n")
        ->get();

        return view("site/admin/fidelidade/index", compact("ativados", "desativados"));
    }

    public function remove(Request $request){
        try{
            $id = $request->input("variavel_produto_id");
            $produto = VariaveisProduto::find($id);
            $produto->pontos = 0;
            $produto->save();

            $success = "Produto removido da fidelidade";
            session()->flash("success", $success);
            
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function add(Request $request){
        try{
            $id = $request->input("variavel_produto_id");
            $pontos = $request->input("pontos");
            $produto = VariaveisProduto::find($id);
            $produto->pontos = $pontos;
            $produto->save();

            $success = "Produto adicionado a fidelidade";
            session()->flash("success", $success);
            
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
}