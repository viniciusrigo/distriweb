<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Caixa;
use App\Models\FluxoCaixa;

class CaixaController extends Controller
{
    
    public function index(){
        $caixa_aberto = Caixa::where('status', 'a')->first();
        if(isset($caixa_aberto)){
            $fluxo = FluxoCaixa::where("caixas_id", $caixa_aberto->id)->get();
            return view("site/admin/caixa/meu-caixa/index", compact("caixa_aberto", "fluxo"));
        }
        return view("site/admin/caixa/meu-caixa/index");
    }

    public function open(Request $request){
        $dados = $request->except('_token');
        Caixa::create($dados);

        $success = 'Caixa aberto com sucesso';
        session()->flash('success', $success);
        return redirect()->back();
    }

    public function close(Request $request){
        $dados = $request->except('_token');

        if ($dados["valor_retirada"] > $dados["saldo_atual"]){
            $error = 'Valor da retirada maior que saldo atual';
            session()->flash('error', $error);
            return redirect()->back();
        }

        $caixa = Caixa::where('status', 'a')->first();
        $caixa["valor_final"] = $dados["saldo_atual"];
        $caixa["valor_retirada"] = $dados["valor_retirada"];
        $caixa["status"] = "f";
        $caixa["data_fechamento"] = now();
        $caixa->save();

        $success = 'Caixa fechado com sucesso';
        session()->flash('success', $success);
        return redirect()->back();
    }

}
