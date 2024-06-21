<?php

namespace App\Http\Controllers\Admin;

use App\Models\FluxoCaixa;
use App\Models\ProdutosPedido;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Caixa;
use App\Models\FluxoBanco;
use App\Models\FormaPagamento;
use App\Models\LocalVenda;
use App\Models\MovimentacoesFinanceira;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use App\Models\Venda;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index(){
        $data = date('Y-m-d', strtotime('-3 days', strtotime(now())));

        $pedidos = Pedido::whereDate('data', ">", $data)->join('forma_pagamentos', 'pedidos.forma_pagamentos_id', '=', 'forma_pagamentos.id')->select(
            'pedidos.id','pedidos.total', 'pedidos.frete', 'pedidos.novo_endereco', 'pedidos.dinheiro', 'pedidos.troco','pedidos.data', 'pedidos.status', 'forma_pagamentos.nome as forma_pagamento'
        )->get()->toArray();

        for($i= 0;$i<count($pedidos);$i++){
            $produtos = ProdutosPedido::where("pedidos_id",$pedidos[$i]["id"])
            ->join('produtos', 'produtos_pedidos.produtos_id', '=', 'produtos.id')
            ->select(
                'produtos.nome', 'produtos_pedidos.qtd'
            )->get()->toArray();
            $pedidos[$i]["produtos"] = $produtos;
        }

        //dd($pedidos);

        return view("site/admin/pedidos/index", compact("pedidos",));
    }

    public function status(Request $request){
        $dados = $request->except("_token");
        $pedido = Pedido::find($dados["pedido"]);
        //dd($pedido);
        if ($dados["status"] == "s") {
            $cliente = User::where("id", $pedido->users_id)->first();
            $caixa = Caixa::where("status", "a")->first('id');
            $forma_pagamento = FormaPagamento::where("id", $pedido->forma_pagamentos_id)->first();
            $banco = Banco::where("id", $forma_pagamento->banco_id)->first();
            
            if($pedido->forma_pagamentos_id == 2){

                /* NOVO FLUXO */
                $fluxo_caixa = new FluxoCaixa();
                $fluxo_caixa->caixas_id = $caixa->id;
                $fluxo_caixa->venda = $pedido->total;
                $fluxo_caixa->dinheiro = $pedido->dinheiro;
                $fluxo_caixa->troco = $pedido->dinheiro - $pedido->total;
                $fluxo_caixa->data = now();
                $fluxo_caixa->save();

                /* NOVA VENDA */
                $venda_temp = new Venda();
                $venda_temp->cpf_cliente = $cliente->cpf;
                $venda_temp->local_id = 2;
                $venda_temp->valor = $pedido->total;
                $venda_temp->lucro = $pedido->lucro;
                $venda_temp->taxa = null;
                $venda_temp->forma_pagamentos_id = $pedido->forma_pagamentos_id;
                $venda_temp->dinheiro = $pedido->dinheiro;
                $venda_temp->troco = $pedido->troco;
                $venda_temp->status = "f";
                $venda_temp->data_venda = $pedido->data;
                $venda_temp->save();

                /* NOVA MOVIMENTAÇÃO FINANCEIRA */
                $nova_mov = new MovimentacoesFinanceira();
                $nova_mov->local_id = 2;
                $nova_mov->cliente_fornecedor = $cliente->cpf;
                $nova_mov->valor = $pedido->total;
                $nova_mov->lucro = $pedido->lucro;
                $nova_mov->forma_pagamentos_id = $pedido->forma_pagamentos_id;
                $nova_mov->tipo = "e";
                $nova_mov->data = $pedido->data;
                $nova_mov->save();

            } else {

                /* NOVA VENDA */
                $venda_temp = new Venda();
                $venda_temp->cpf_cliente = $cliente->cpf;
                $venda_temp->local_id = 2;
                $venda_temp->valor = $pedido->total - (round( ($pedido->total  / 100) * $forma_pagamento->taxa, 2));
                $venda_temp->lucro = $pedido->lucro - (round( ($pedido->total  / 100) * $forma_pagamento->taxa, 2));
                $venda_temp->taxa = $forma_pagamento->taxa == 0 ? null : $forma_pagamento->taxa;
                $venda_temp->forma_pagamentos_id = $pedido->forma_pagamentos_id;
                $venda_temp->dinheiro = null;
                $venda_temp->troco = null;
                $venda_temp->status = "f";
                $venda_temp->data_venda = $pedido->data;
                $venda_temp->save();

                /* NOVA MOVIMENTAÇÃO FINANCEIRA */
                $nova_mov = new MovimentacoesFinanceira();
                $nova_mov->local_id = 2;
                $nova_mov->cliente_fornecedor = $cliente->cpf;
                $nova_mov->valor = $pedido->total - (round( ($pedido->total  / 100) * $forma_pagamento->taxa, 2));
                $nova_mov->lucro = $pedido->lucro - (round( ($pedido->total  / 100) * $forma_pagamento->taxa, 2));
                $nova_mov->forma_pagamentos_id = $pedido->forma_pagamentos_id;
                $nova_mov->tipo = "e";
                $nova_mov->data = $pedido->data;
                $nova_mov->save();
            }

            $cliente->pontos += $pedido->pontos;
            $cliente->save();

            $fluxo_banco = new FluxoBanco();
            $fluxo_banco->local_id = $banco->id;
            $fluxo_banco->valor = $venda_temp["valor"];
            $fluxo_banco->tipo = "e";
            $fluxo_banco->data = now();
            $fluxo_banco->save();

            $banco->saldo += $pedido->total;
            $banco->save();
        }


        $pedido["status"] = $dados["status"];
        $pedido->save();

        return redirect()->back();
    }

    public function imprimir_pedido(string|int $id){
        
        $numero = intval($id);

        $pedido = Pedido::join('forma_pagamentos', 'pedidos.forma_pagamentos_id', '=', 'forma_pagamentos.id')->select(
            'pedidos.*','forma_pagamentos.nome as forma_pagamento'
        )->where('pedidos.id', '=', $numero)->first()->toArray();

        $pedido['produtos'] = ProdutosPedido::where("pedidos_id", $numero)->join('produtos', 'produtos_pedidos.produtos_id', '=', 'produtos.id')->select(
            'produtos.nome', 'produtos_pedidos.qtd'
        )->get()->toArray();
        //dd($pedido);
        return view("site/admin/pedidos/imprimir", compact("pedido"));
    }

    public function rejeitar(Request $request){
        $dado = $request->except('_token');
        $produtos = ProdutosPedido::where("pedidos_id", $dado["pedido"])->get();
        //dd($produtos);
        for($i = 0; $i < count($produtos); $i++){
            $produto_estoque = Produto::where("id", $produtos[$i]->produtos_id)->first();
            $produto_estoque->quantidade += $produtos[$i]->qtd;
            $produto_estoque->save();

            $produtos[$i]->delete();
        }
        Pedido::find($dado["pedido"])->delete();

        $success = "Pedido rejeitado com sucesso";
        session()->flash("success", $success);
        return redirect()->back();
    }


}
