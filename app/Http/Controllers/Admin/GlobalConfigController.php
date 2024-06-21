<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\FormaPagamento;
use App\Models\LocalVenda;
use App\Models\TipoConta;

class GlobalConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $bancos = Banco::all('id', 'nome', 'agencia', 'conta');
        $forma_pagamentos = FormaPagamento::join('bancos', 'forma_pagamentos.banco_id', '=', 'bancos.id')->select(
            'forma_pagamentos.id', 'forma_pagamentos.nome', 'forma_pagamentos.taxa', 'bancos.nome as banco_nome'
        )->get();
        $tipo_contas = TipoConta::all('tipo_conta');
        $locais = LocalVenda::all();

        return view("site/admin/configuracao-global/index", compact('bancos', 'forma_pagamentos', 'tipo_contas', 'locais'));
    }

    public function store(Request $request){
        $req = $request->except('_token', 'Contas_a_Pagar-credito', 'Contas_a_Pagar-debito');
        //dd($req);
        $dados[0] = ["c" => $req["Online-credito"], "d" => $req["Online-debito"]];
        $dados[1] = ["c" => $req["Comanda-credito"], "d" => $req["Comanda-debito"]];
        $dados[2] = ["c" => $req["PDV1-credito"], "d" => $req["PDV1-debito"]];
        //dd($dados);
        $locais = LocalVenda::where("id", "!=", 1)->get();
        //dd($locais);
        for($i = 0; $i < count($locais); $i++){
            $locais[$i]->credito_id = $dados[$i]["c"];
            $locais[$i]->debito_id = $dados[$i]["d"];
            $locais[$i]->save();
        }

        return redirect()->back();
    }

    public function novo_banco(Request $request){
        $dados = $request->except('_token');
        $dados['saldo'] = str_replace(',', '.', $dados['saldo']);

        Banco::create($dados);

        $success = "Banco cadastrado com sucesso";
        session()->flash("success", $success);
        return redirect()->back();
    }

    public function nova_forma_pagamento(Request $request){
        $dados = $request->except('_token');
        $dados['taxa'] = str_replace(',', '.', $dados['taxa']);

        FormaPagamento::create($dados);
        
        $success = "Forma de pagamento cadastrada com sucesso";
        session()->flash("success", $success);
        return redirect()->back();
    }

    public function novo_tipo_conta(Request $request){
        $dados = $request->except('_token');
        
        TipoConta::create($dados);

        $success = "Tipo de conta cadastrada com sucesso";
        session()->flash("success", $success);
        return redirect()->back();

    }

}
