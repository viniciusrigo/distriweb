<?php

namespace App\Http\Controllers\Admin;

use App\Models\FluxoCaixa;
use App\Models\ProdutosPedido;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Caixa;
use App\Models\CombosProduto;
use App\Models\FluxoBanco;
use App\Models\FormaPagamento;
use App\Models\GlobalConfig;
use App\Models\MovimentacoesFinanceira;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use App\Models\VariaveisProduto;
use App\Models\Venda;
use Exception;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function page_index(){
        $data = date("Y-m-d", strtotime("-3 days", strtotime(now())));

        $pedidos = Pedido::whereDate("data", ">", $data)
        ->join("forma_pagamentos", "pedidos.forma_pagamento_id", "=", "forma_pagamentos.id")
        ->select(
            "pedidos.id",
            "pedidos.total",
            "pedidos.frete",
            "pedidos.novo_endereco",
            "pedidos.dinheiro",
            "pedidos.troco",
            "pedidos.data",
            "pedidos.status",
            "forma_pagamentos.nome as forma_pagamento"
        )->get()->toArray();

        for($i= 0; $i < count($pedidos); $i++){
            $variaveis = ProdutosPedido::where("pedido_id",$pedidos[$i]["id"])
            ->join("produtos", "produtos_pedidos.produto_id", "=", "produtos.id")
            ->join("variaveis_produtos", "produtos_pedidos.variavel_produto_id", "=", "variaveis_produtos.id")
            ->select(
                "produtos.nome",
                "variaveis_produtos.variavel_nome"
            )->get()->toArray();
            $pedidos[$i]["produtos"] = $variaveis;
        }

        return view("site/admin/pedidos/index", compact("pedidos",));
    }
    
    public function status(Request $request){
        
            $dados = $request->except("_token");
            $pedido = Pedido::where("id", $dados["pedido_id"])->first();
            
            if ($dados["status"] == "s") {
                $cliente = User::where("id", $pedido->user_id)->first();
                $caixa = Caixa::where("status", "a")->first("id");
                $forma_pagamento = FormaPagamento::where("id", $pedido->forma_pagamento_id)->first();
                $banco = Banco::where("id", $forma_pagamento->banco_id)->first();
                $produtos = ProdutosPedido::where("pedido_id", $pedido->id)->get()->toArray();

                //dd($produtos);
                for($x = 0; $x < count($produtos); $x++){
                    $variavel = VariaveisProduto::find($produtos[$x]["variavel_produto_id"]);
                    //dd($variavel);
                    $produto = Produto::find($variavel->produto_id);

                    if($produto->categoria_id != 5 && $produto->categoria_id != 6){
                        $variavel->variavel_quantidade -= 1;
                        $variavel->save();
                    } else if($produto->categoria_id == 5) {
                        $combo_produtos = CombosProduto::where("produto_id", $produto->id)->get(["variavel_produto_id", "combo_quantidade"]);
                        for($i = 0; $i < count($combo_produtos); $i++){
                            $variavel = VariaveisProduto::where("id", $combo_produtos[$i]->variavel_produto_id)->first();
                            $variavel->variavel_quantidade -=  $combo_produtos[$i]->combo_quantidade;
                            $variavel->save();
                        }
                    } else {
                        $fardo_variavel = VariaveisProduto::where("id", $produto->variavel_produto_id)->first();
                        $fardo_variavel->variavel_quantidade -=  $produto->fardo_quantidade;
                        $fardo_variavel->save();
                    }
                    //dd($variavel);
                }
                
                if($pedido->forma_pagamento_id == 2){

                    /* NOVO FLUXO */
                    $fluxo_caixa = new FluxoCaixa();
                    $fluxo_caixa->caixas_id = $caixa->id;
                    $fluxo_caixa->venda = $pedido->total;
                    $fluxo_caixa->dinheiro = $pedido->dinheiro;
                    $fluxo_caixa->troco = $pedido->dinheiro - $pedido->total;
                    $fluxo_caixa->data = now();

                    /* NOVA VENDA */
                    $venda_temp = new Venda();
                    $venda_temp->cpf_cliente = $cliente->cpf;
                    $venda_temp->local_id = 2;
                    $venda_temp->valor = $pedido->total;
                    $venda_temp->lucro = $pedido->lucro;
                    $venda_temp->taxa = null;
                    $venda_temp->pedido_id = $pedido->id;
                    $venda_temp->forma_pagamento_id = $pedido->forma_pagamento_id;
                    $venda_temp->dinheiro = $pedido->dinheiro;
                    $venda_temp->troco = $pedido->troco;
                    $venda_temp->status = "f";
                    $venda_temp->data_venda = $pedido->data;

                    /* NOVA MOVIMENTAÇÃO FINANCEIRA */
                    $nova_mov = new MovimentacoesFinanceira();
                    $nova_mov->local_id = 2;
                    $nova_mov->cliente_fornecedor = $cliente->cpf;
                    $nova_mov->valor = $pedido->total;
                    $nova_mov->lucro = $pedido->lucro;
                    $nova_mov->forma_pagamento_id = $pedido->forma_pagamento_id;
                    $nova_mov->tipo = "e";
                    $nova_mov->data = $pedido->data;
                    
                    $fluxo_banco = new FluxoBanco();
                    $fluxo_banco->banco_id = $banco->id;
                    $fluxo_banco->valor = $venda_temp["valor"];
                    $fluxo_banco->tipo = "e";
                    $fluxo_banco->data = now();      
                    
                    $cliente->pontos -= $pedido->pontos_troca;
                    $cliente->pontos += $pedido->pontos;
                    
                    $banco->saldo += $pedido->total;    
                    
                    DB::transaction(function() use ($fluxo_caixa, $venda_temp, $nova_mov, $fluxo_banco, $cliente, $banco){
                        $fluxo_caixa->save();
                        $venda_temp->save();
                        $nova_mov->save();
                        $fluxo_banco->save();
                        $cliente->save();
                        $banco->save();
                    });

                } else {

                    /* NOVA VENDA */
                    $venda_temp = new Venda();
                    $venda_temp->cpf_cliente = $cliente->cpf;
                    $venda_temp->local_id = 2;
                    $venda_temp->valor = $pedido->total - (round( ($pedido->total  / 100) * $forma_pagamento->taxa, 2));
                    $venda_temp->lucro = $pedido->lucro - (round( ($pedido->total  / 100) * $forma_pagamento->taxa, 2));
                    $venda_temp->taxa = $forma_pagamento->taxa == 0 ? null : $forma_pagamento->taxa;
                    $venda_temp->pedido_id = $pedido->id;
                    $venda_temp->forma_pagamento_id = $pedido->forma_pagamento_id;
                    $venda_temp->dinheiro = null;
                    $venda_temp->troco = null;
                    $venda_temp->status = "f";
                    $venda_temp->data_venda = $pedido->data;

                    /* NOVA MOVIMENTAÇÃO FINANCEIRA */
                    $nova_mov = new MovimentacoesFinanceira();
                    $nova_mov->local_id = 2;
                    $nova_mov->cliente_fornecedor = $cliente->cpf;
                    $nova_mov->valor = $pedido->total - (round( ($pedido->total  / 100) * $forma_pagamento->taxa, 2));
                    $nova_mov->lucro = $pedido->lucro - (round( ($pedido->total  / 100) * $forma_pagamento->taxa, 2));
                    $nova_mov->forma_pagamento_id = $pedido->forma_pagamento_id;
                    $nova_mov->tipo = "e";
                    $nova_mov->data = $pedido->data;

                    $fluxo_banco = new FluxoBanco();
                    $fluxo_banco->banco_id = $banco->id;
                    $fluxo_banco->valor = $venda_temp["valor"];
                    $fluxo_banco->tipo = "e";
                    $fluxo_banco->data = now();

                    $cliente->pontos -= $pedido->pontos_troca;
                    $cliente->pontos += $pedido->pontos;

                    $banco->saldo += $pedido->total;

                    DB::transaction(function() use ($venda_temp, $nova_mov, $fluxo_banco, $cliente, $banco){
                        $venda_temp->save();
                        $nova_mov->save();
                        $fluxo_banco->save();
                        $cliente->save();
                        $banco->save();
                    });
                }

            }



            $pedido["status"] = $dados["status"];
            $pedido->save();

            return redirect()->back();
        
    }

    public function imprimir_pedido(string|int $id){
        
        $numero = intval($id);

        $pedido = Pedido::join("forma_pagamentos", "pedidos.forma_pagamento_id", "=", "forma_pagamentos.id")->select(
            "pedidos.*","forma_pagamentos.nome as forma_pagamento"
        )->where("pedidos.id", "=", $numero)->first()->toArray();

        $pedido["produtos"] = ProdutosPedido::where("pedido_id", $numero)
        ->join("produtos", "produtos_pedidos.produto_id", "=", "produtos.id")
        ->join("variaveis_produtos", "produtos_pedidos.variavel_produto_id", "=", "variaveis_produtos.id")
        ->select(
            "produtos.nome", "variaveis_produtos.variavel_nome"
        )->get()->toArray();

        $info_empresa = GlobalConfig::find(1)->toArray();

        //dd($pedido);
        return view("site/admin/pedidos/imprimir", compact("pedido", "info_empresa"));
    }

    public function reject(Request $request){
        try {
            $dado = $request->except("_token");
            $produtos = ProdutosPedido::where("pedido_id", $dado["pedido_id"])->get();

            for($i = 0; $i < count($produtos); $i++){
                $produto_estoque = Produto::where("id", $produtos[$i]->produto_id)->first();
                $produto_estoque->quantidade += $produtos[$i]->quantidade;
                $produto_estoque->save();

                $produtos[$i]->delete();
            }

            Pedido::find($dado["pedido"])->delete();

            $success = "Pedido rejeitado com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
