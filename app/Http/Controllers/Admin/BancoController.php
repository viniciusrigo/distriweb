<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Caixa;
use App\Models\FluxoBanco;
use App\Models\FluxoCaixa;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BancoController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function page_index(){
        $bancos = Banco::all();

        $inicio = date("Y-m-01 00:00:00", strtotime(now()));
        $fim = date("Y-m-d H:i:s", strtotime(now()));
        for($i = 0; $i < count($bancos); $i++){
            $fluxos = FluxoBanco::where("banco_id", $bancos[$i]->id)->orderBy("data", "desc")->whereBetween("data", [$inicio, $fim])->get();
            $bancos[$i]->fluxos = $fluxos;
        }

        return view('site/admin/bancos/index', compact('bancos'));
    }

    public function mov_extra(Request $request){
        try{
            $dados_request = $request->except("_token");
            $dados_request["valor"] = str_replace(',', '.', $dados_request["valor"]);
            $fluxo = new FluxoBanco();
            $banco = Banco::find($dados_request["banco_id"]);

            if($banco->id == 1){
                $caixa_aberto = Caixa::where("status", "a")->first();
                $fluxo_caixa = new FluxoCaixa();
                $fluxo_caixa->caixa_id = $caixa_aberto->id;
                $fluxo_caixa->venda = 0;
                if($dados_request["acao"] == "Remover"){
                    $fluxo_caixa->dinheiro = 0;
                    $fluxo_caixa->troco = $dados_request["valor"];
                } else {
                    $fluxo_caixa->dinheiro = $dados_request["valor"];
                    $fluxo_caixa->troco = 0;
                }
                $fluxo_caixa->data = now();
                $fluxo_caixa->save();
            }

            if($dados_request["acao"] == "Remover"){
                $fluxo->banco_id = $banco->id;
                $fluxo->valor = $dados_request["valor"];
                $fluxo->tipo = "s";
                $fluxo->mov_extra = "s";
                $fluxo->motivo = $dados_request["motivo"];
                $fluxo->data = now();
            } else {
                $fluxo->banco_id = $banco->id;
                $fluxo->valor = $dados_request["valor"];
                $fluxo->tipo = "e";
                $fluxo->mov_extra = "s";
                $fluxo->motivo = $dados_request["motivo"];
                $fluxo->data = now();
            }
            
            
            $banco->saldo += $dados_request["valor"];

            DB::transaction(function() use ($banco, $fluxo){
                $fluxo->save();
                $banco->save();
            });

            $success = "Movimentação Extraordinária criada com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}