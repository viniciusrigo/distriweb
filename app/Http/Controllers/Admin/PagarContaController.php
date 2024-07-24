<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Caixa;
use App\Models\FluxoBanco;
use App\Models\FluxoCaixa;
use App\Models\Fornecedor;
use App\Models\MovimentacoesFinanceira;
use App\Models\PagarConta;
use App\Models\TipoConta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagarContaController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth");
    }

    public function page_index(){
        $contas = DB::table("pagar_contas")
        ->where("pagar_contas.status", "=", "a")
        ->join("tipo_contas","pagar_contas.conta_id","=","tipo_contas.id")
        ->join("fornecedores","pagar_contas.fornecedor_id","=","fornecedores.id")
        ->join("bancos","pagar_contas.banco_id","=","bancos.id")
        ->select(
            "pagar_contas.id",
            "bancos.nome as banco_nome",
            "pagar_contas.banco_id",
            "tipo_contas.tipo_conta",
            "fornecedores.nome",
            "pagar_contas.vencimento",
            "pagar_contas.valor",
            "pagar_contas.status"
        )->orderByRaw("pagar_contas.vencimento asc")
        ->take(100)->get();

        $contasPagas = DB::table("pagar_contas")
        ->where("pagar_contas.status", "=", "p")
        ->join("tipo_contas","pagar_contas.conta_id","=","tipo_contas.id")
        ->join("fornecedores","pagar_contas.fornecedor_id","=","fornecedores.id")
        ->join("bancos","pagar_contas.banco_id","=","bancos.id")
        ->select(
            "pagar_contas.id",
            "bancos.nome as banco_nome",
            "pagar_contas.banco_id",
            "tipo_contas.tipo_conta",
            "fornecedores.nome",
            "pagar_contas.vencimento",
            "pagar_contas.data_pagamento",
            "pagar_contas.valor",
            "pagar_contas.status"
        )->orderByRaw("pagar_contas.vencimento desc")
        ->take(100)->get();

        $bancos = Banco::all("id", "nome");
        $tipos_contas = TipoConta::all();
        $fornecedores = Fornecedor::all("id", "nome");

        return view("site/admin/financeiro/contas-a-pagar/index", compact("contas", "contasPagas", "bancos", "tipos_contas", "fornecedores"));
    }

    public function store(Request $request, PagarConta $pagarConta){
        try {
            $dados = $request->all();
            $timestamp = strtotime($dados["vencimento"]);
            $timestamp += 72000;
            $dados["vencimento"] = date("Y-m-d H:i:s", $timestamp);

            $dados["valor"] = str_replace(",", ".", $dados["valor"]);

            if($dados["status"] == "p"){
                $dados["data_pagamento"] = now();

                $caixa_aberto = Caixa::where("status", "a")->first();
                if($dados["banco_id"] == 1){
                    $novo_fluxo = new FluxoCaixa();
                    $novo_fluxo["caixa_id"] = $caixa_aberto->id;
                    $novo_fluxo["venda"] = 0;
                    $novo_fluxo["dinheiro"] = 0;
                    $novo_fluxo["troco"] = $dados["valor"];
                    $novo_fluxo["data"] = now(); 
                    $novo_fluxo->save();
                }
                $banco = Banco::where("id", $dados["banco_id"])->first();
                $banco->saldo -= $dados["valor"];
                
                $fluxo_banco = new FluxoBanco();
                $fluxo_banco->banco_id = $banco->id;
                $fluxo_banco->valor = $dados["valor"];
                $fluxo_banco->tipo = "s";
                $fluxo_banco->data = now();
                
                $mov_fin = new MovimentacoesFinanceira;
                $mov_fin["local_id"] = 1;
                $mov_fin["cliente_fornecedor"] = $dados["fornecedor_id"];
                $mov_fin["valor"] = $dados["valor"];
                $mov_fin["forma_pagamento_id"] = 1;
                $mov_fin["tipo"] = "s";
                $mov_fin["data"] = now();
                
                DB::transaction(function() use ($mov_fin, $fluxo_banco, $banco){
                    $banco->save();
                    $fluxo_banco->save();
                    $mov_fin->save();
                });
            }

            $pagarConta->create($dados);
            
            $success = "Conta adiciona com sucesso";
            session()->flash("cadastro-success", $success);
            
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(string|int $id, PagarConta $pagarConta){
        try{
            if (!$pagarConta = $pagarConta->find($id)) {
                return redirect()->back();
            }

            $pagarConta->delete();

            $success = "Conta excluída com sucesso";
            session()->flash("excluida-success", $success);

            return redirect()->route("admin.financeiro.contas-a-pagar.index");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function pay(string|int $id){
        try{
            $conta = PagarConta::find($id);
            $banco = Banco::where("id", $conta->banco_id)->first();

            if($banco->saldo < $conta->valor) {
                $error = "Saldo insuficiente em ".$banco->nome;
                session()->flash("error", $error);

                return redirect()->back();
            }

            if($banco->id == 1){
                $caixa_aberto = Caixa::where("status", "a")->first();
                $fluxo_caixa = new FluxoCaixa();
                $fluxo_caixa->caixa_id = $caixa_aberto->id;
                $fluxo_caixa->venda = 0;
                $fluxo_caixa->dinheiro = 0;
                $fluxo_caixa->troco = $conta->valor;
                $fluxo_caixa->save();
            }
            $banco->saldo -= $conta->valor;
            
            $fluxo_banco = new FluxoBanco();
            $fluxo_banco->banco_id = $banco->id;
            $fluxo_banco->valor = $conta->valor;
            $fluxo_banco->tipo = "s";
            $fluxo_banco->data = now();
            
            $conta->data_pagamento = date("Y-m-d H:i:s", time());
            $conta->status = "p";
            
            $mov_fin = new MovimentacoesFinanceira;
            $mov_fin["local_id"] = 1;
            $mov_fin["cliente_fornecedor"] = $conta->fornecedor_id;
            $mov_fin["valor"] = $conta->valor;
            $mov_fin["forma_pagamento_id"] = 1;
            $mov_fin["tipo"] = "s";
            $mov_fin["data"] = $conta["data_pagamento"];
            
            DB::transaction(function() use ($banco, $fluxo_banco, $conta, $mov_fin){
                $banco->save();
                $fluxo_banco->save();
                $conta->save();
                $mov_fin->save();
            });

            $success = "Conta paga com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}