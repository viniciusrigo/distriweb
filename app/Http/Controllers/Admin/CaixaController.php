<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Caixa;
use App\Models\FluxoCaixa;

class CaixaController extends Controller
{
    
    public function index(){
        $caixa_aberto = Caixa::where('status', 'a')->first();
        if(isset($caixa_aberto)){
            $fluxo = FluxoCaixa::where("caixas_id", $caixa_aberto->id)->get();
            $banco = Banco::first();

            return view("site/admin/caixa/meu-caixa/index", compact("caixa_aberto", "fluxo", 'banco'));
        }

        $caixas = Caixa::where('status', 'f')->get();

        $ult_caixa = Caixa::where('status', 'f')->orderBy('id', 'desc')->first();
        
        return view("site/admin/caixa/meu-caixa/index", compact('ult_caixa', 'caixas'));
    }

    public function open(Request $request){
        $dados = $request->except('_token');

        $banco = Banco::first();
        if($banco->saldo == 0.00){
            $banco->saldo = $dados["valor_inicial"];
            $banco->save();
        }

        Caixa::create($dados);
        
        $success = 'Caixa aberto com sucesso';
        session()->flash('success', $success);
        return redirect()->back();
    }

    public function close(Request $request){
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
        $banco->save();

        $caixa = Caixa::where('status', 'a')->first();
        $caixa["valor_final"] = $dados["saldo_atual"] - $dados["valor_retirada"];
        $caixa["valor_retirada"] = $dados["valor_retirada"];
        $caixa["status"] = "f";
        $caixa["data_fechamento"] = now();
        $caixa->save();

        $success = 'Caixa fechado com sucesso';
        session()->flash('success', $success);
        return redirect()->back();
    }

}
