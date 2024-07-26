<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Caixa;
use App\Models\FluxoBanco;
use App\Models\FluxoCaixa;
use Exception;
use Illuminate\Support\Facades\DB;

class CaixaController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }
    
    public function page_index(){
        $caixa_aberto = Caixa::where('status', 'a')->first();
        if(isset($caixa_aberto)){
            $fluxo = FluxoCaixa::where("caixa_id", $caixa_aberto->id)->get();
            $banco = Banco::first();
            $caixas = Caixa::where('status', 'f')->get();

            return view("site/admin/caixa/meu-caixa/index", compact("caixa_aberto", "fluxo", 'banco', 'caixas'));
        }

        $caixas = Caixa::where('status', 'f')->take(50)->get();

        $ult_caixa = Caixa::where('status', 'f')->orderBy('id', 'desc')->first();
        
        return view("site/admin/caixa/meu-caixa/index", compact('ult_caixa', 'caixas'));
    }

    public function open(Request $request){
        
        try{
            $dados = $request->except('_token');

            $banco = Banco::first();
            /* PRIMEIRA ABERTURA DE CAIXA */
            if($banco->saldo == 0.00){

                $fluxo_banco = new FluxoBanco();
                $fluxo_banco->banco_id = 1;
                $fluxo_banco->valor = $dados["valor_inicial"];
                $fluxo_banco->tipo = "e";
                $fluxo_banco->data = now();
                
                $banco->saldo = $dados["valor_inicial"];

                DB::transaction(function() use ($banco, $dados, $fluxo_banco){
                    $fluxo_banco->save();
                    $banco->save();
                    Caixa::create($dados);
                });
            }

            DB::transaction(function() use ($banco, $dados){
                $banco->save();
                Caixa::create($dados);
            });
            
            $success = 'Caixa aberto com sucesso';
            session()->flash('success', $success);
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function close(Request $request){

        try{
            $dados = $request->except('_token');
            $dados["valor_retirada"] = str_replace(',', '.', $dados["valor_retirada"]);
            $dados["saldo_atual"] = floatval($dados["saldo_atual"]);
            $dados["valor_retirada"] = floatval($dados["valor_retirada"]);
            
            if ($dados["valor_retirada"] > $dados["saldo_atual"]){
                $error = 'Valor da retirada maior que saldo atual';
                session()->flash('error', $error);
                return redirect()->back();
            }

            $banco = Banco::first();
            $banco->saldo -= $dados["valor_retirada"];
            
            $fluxo_banco = new FluxoBanco();
            $fluxo_banco->banco_id = 1;
            $fluxo_banco->valor = $dados["valor_retirada"];
            $fluxo_banco->tipo = "s";
            $fluxo_banco->data = now();
            
            $caixa = Caixa::where('status', 'a')->first();
            $caixa["valor_final"] = $dados["saldo_atual"] - $dados["valor_retirada"];
            $caixa["valor_retirada"] = $dados["valor_retirada"];
            $caixa["status"] = "f";
            $caixa["data_fechamento"] = now();

            DB::transaction(function() use ($banco, $fluxo_banco, $caixa){
                $banco->save();
                $fluxo_banco->save();
                $caixa->save();
            });

            $success = 'Caixa fechado com sucesso';
            session()->flash('success', $success);
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
