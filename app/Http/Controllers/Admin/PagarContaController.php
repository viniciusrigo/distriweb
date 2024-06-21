<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\MovimentacoesFinanceira;
use App\Models\PagarConta;
use App\Models\TipoConta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagarContaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $contas = DB::table("pagar_contas")       //tabela 2      //tabela e coluna 1       //tabela e coluna 2
                                            ->where("pagar_contas.status", "=", "a")
                                            ->join("tipo_contas","pagar_contas.conta_id","=","tipo_contas.id")
                                            ->join("fornecedores","pagar_contas.fornecedor_id","=","fornecedores.id")
                                            ->join("bancos","pagar_contas.banco_id","=","bancos.id")
                                            ->select('pagar_contas.id', 'bancos.nome as banco_nome', 'pagar_contas.banco_id', 'tipo_contas.tipo_conta','fornecedores.nome','pagar_contas.vencimento','pagar_contas.valor','pagar_contas.status')
                                            ->orderByRaw('pagar_contas.vencimento asc')
                                            ->take(100)->get();

        $contasPagas = DB::table("pagar_contas")       //tabela 2      //tabela e coluna 1       //tabela e coluna 2
                                            ->where("pagar_contas.status", "=", "p")
                                            ->join("tipo_contas","pagar_contas.conta_id","=","tipo_contas.id")
                                            ->join("fornecedores","pagar_contas.fornecedor_id","=","fornecedores.id")
                                            ->join("bancos","pagar_contas.banco_id","=","bancos.id")
                                            ->select('pagar_contas.id', 'bancos.nome as banco_nome', 'pagar_contas.banco_id', 'tipo_contas.tipo_conta','fornecedores.nome','pagar_contas.vencimento', 'pagar_contas.data_pagamento','pagar_contas.valor','pagar_contas.status')
                                            //->orderByRaw('day(pagar_contas.vencimento) desc')
                                            ->orderByRaw('pagar_contas.vencimento desc')
                                            ->take(100)->get();
        // for($i= 0;$i<count($contas);$i++){
        //     $contas[$i]->vencimento = date('d/m/Y', strtotime($contas[$i]->vencimento));
        // }

        $bancos = Banco::all('id', 'nome');
        $tipos_contas = TipoConta::all();

        return view("site/admin/financeiro/contas-a-pagar/index", compact('contas', 'contasPagas', 'bancos', 'tipos_contas'));
    }

    public function store(Request $request, PagarConta $pagarConta){

        $dados = $request->all();

        $dados['valor'] = str_replace(',', '.', $dados['valor']);

        $pagarConta->create($dados);
        
        $success = "Conta adiciona com sucesso";
        session()->flash("cadastro-success", $success);
        return redirect()->back();
    }

    public function destroy(string|int $id, PagarConta $pagarConta){
        if (!$pagarConta = $pagarConta->find($id)) {
            return redirect()->back();
        }
        $pagarConta->delete();

        $success = "Conta excluída com sucesso";
        session()->flash("excluida-success", $success);
        return redirect()->route('admin.financeiro.contas-a-pagar.index');
    }

    public function pagar(string|int $id){
        $conta = PagarConta::find($id);

        $banco = Banco::where("id", $conta->banco_id)->first();
        if($banco->saldo < $conta->valor) {
            $error = "Saldo insuficiente em ".$banco->nome;
            session()->flash("error", $error);
            return redirect()->back();
        }
        $banco->saldo -= $conta->valor;
        $banco->save();

        $conta->data_pagamento = date("Y-m-d H:i:s", time());
        $conta->status = "p";
        $conta->save();

        $mov_fin = new MovimentacoesFinanceira;
        $mov_fin["local_id"] = 1;
        $mov_fin["cliente_fornecedor"] = $conta->fornecedor_id;
        $mov_fin["valor"] = $conta->valor;
        $mov_fin["forma_pagamentos_id"] = 1;
        $mov_fin["tipo"] = 's';
        $mov_fin['data'] = $conta['data_pagamento'];
        $mov_fin->save();

        $success = "Conta paga com sucesso";
        session()->flash("success", $success);
        return redirect()->back();
    }

}
