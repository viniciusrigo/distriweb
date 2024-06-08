<?php

namespace App\Http\Controllers\Vendedor;

use App\Models\ItemVenda;
use App\Models\ProdutosVenda;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CarrinhoVenda;
use App\Models\FormaPagamento;
use App\Models\Produto;
use App\Models\MovimentacoesFinanceira;
use App\Models\User;
use App\Models\Venda;

class VendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        /* VERIFICA SE EXISTE VENDA ABERTA, CASO EXISTA REDIRECIONA PARA VENDA ABERTA */
        $venda = Venda::where("status", "=", "a")->get();
        if(count($venda) > 0){
            return redirect("vendedor/pdv/venda/".$venda[0]->id);
        }
        
        return view("site/vendedor/PDV/index");
    }

    public function create(Request $request){

        $nova_venda = $request->all();
        
        if(count(Venda::where("data_venda", "=", $nova_venda["data_venda"])->get()) > 0){

            $venda = Venda::where("data_venda", "=", $nova_venda["data_venda"])->get();

            return redirect("vendedor/pdv/venda/".$venda[0]->id);
        } else{

            Venda::create($nova_venda); 

            $venda = Venda::where("data_venda", "=", $nova_venda["data_venda"])->get();
            
            return redirect("vendedor/pdv/venda/".$venda[0]->id);
        }
    }

    public function venda(){

        $venda = Venda::where("status", "=", "a")->get();

        $qtd = count($venda);
        if ($qtd > 0) {
            $carrinho = CarrinhoVenda::where("vendas_id","=", $venda[0]->id)->join("produtos", "carrinho_vendas.produtos_id", "=", "produtos.id")
                                                                        ->select("produtos.nome", "produtos.preco", "produtos.preco_promocao", "produtos.promocao", "produtos.codigo_barras", "carrinho_vendas.data_adicao")
                                                                        ->get();
            
            return view("site/vendedor/PDV/venda", compact("venda", "carrinho"));

        } else {
            return redirect("vendedor/pdv");
        }
    }

    public function add_produto(Request $request){

        $codigo_barras = $request->input("codigo_barras");
        $vendas_id = $request->input("vendas_id");
        $produto = Produto::where("codigo_barras", "=", $codigo_barras)->first();
        $venda = Venda::where("id", "=", $vendas_id)->first();

        /* VERIFICA SE O CÓDIGO DE BARRAS É VALIDO */
        if ($codigo_barras == null || $codigo_barras == "") {

            $error = "Informe o código de barras";
            session()->flash("alerta", $error);

            return redirect()->back();
        }

        /* VERIFICA SE O PRODUTO EXISTE */
        if(Produto::where("codigo_barras", $codigo_barras)->first() === []){

            $error = "Produto não encontrado";
            session()->flash("error", $error);

            return redirect()->back();
        }

        /* VERIFICA SE TEM O PRODUTO NO ESTOQUE */
        if ($produto->quantidade == 0) {

            $error = "Produto indisponível, verifique o(s) Estoque/Lotes";
            session()->flash("error", $error);

            return redirect()->back();
        } else {
            
            /* ATUALIZA A QUANTIDADE DO PRODUTO EM ESTOQUE */
            $produto->quantidade -= 1;
            $produto->ult_compra = now(); 

            /* ATUALIZA PREÇO/LUCRO/PONTOS */
            if ($produto->promocao == "n"){
                if ($produto->pontos > 0){
                    $venda->valor += $produto->preco;
                    $venda->lucro += $produto->lucro;
                    $venda->pontos += round($produto->preco, 0, PHP_ROUND_HALF_DOWN);
                    $venda->save();
                } else {
                    $venda->valor = $venda->valor + $produto->preco;
                    $venda->lucro += $produto->lucro;
                    $venda->save();
                }
            } else {
                if ($produto->pontos > 0){
                    $venda->valor += $produto->preco_promocao;
                    $venda->lucro += ($produto->preco_promocao - $produto->preco_custo);
                    $venda->pontos += round($produto->preco_promocao, 0, PHP_ROUND_HALF_DOWN);
                    $venda->save();
                } else {
                    $venda->valor += $produto->preco_promocao;
                    $venda->lucro += ($produto->preco_promocao - $produto->preco_custo);
                    $venda->save();
                }
            }      

            /* ADICIONA PRODUTO NO CARRINHO */
            $carrinho = new CarrinhoVenda();
            $carrinho["vendas_id"] = $vendas_id;
            $carrinho["produtos_id"] = $produto->id;
            $carrinho["data_adicao"] = now();
            $carrinho->save();

            return redirect()->back();
        }
    }

    public function remove_produto(Request $request){

        $codigo_barras_remover = $request->input("codigo_barras_remover");
        $vendas_id = $request->input("vendas_id");
        $venda = Venda::where("id", "=", $vendas_id)->first();
        $produto = Produto::where("codigo_barras", "=", $codigo_barras_remover)->first();

        if ($codigo_barras_remover == null || $codigo_barras_remover == "") {
            $error = "Informe o código de barras";
            session()->flash("alerta", $error);
            return redirect()->back();
        }
        if(Produto::where("codigo_barras", $codigo_barras_remover)->first()->toArray() === []){
            $error = "Produto não encontrado";
            session()->flash("error", $error);
            return redirect()->back();
        }

        if (CarrinhoVenda::where("vendas_id", "=", $vendas_id)->where("produtos_id", "=", $produto->id)->take(1)->get()->toArray() === []) {
            $error = "O produto informado no momento não está no carrinho";
            session()->flash("error", $error);
            return redirect()->back();
        }

        /* ATUALIZA ESTOQUE DO PRODUTO */
        $produto->quantidade -= 1;
        $produto->save();

        /* ATUALIZA VALOR TOTAL DA COMPRA */
        if ($produto->promocao == "n"){
            if ($produto->pontos > 0){
                $venda->valor -= $produto->preco;
                $venda->lucro -= $produto->lucro;
                $venda->pontos -= round($produto->preco, 0, PHP_ROUND_HALF_DOWN);
                $venda->save();
            } else {
                $venda->valor -= $produto->preco;
                $venda->lucro -= $produto->lucro;
                $venda->save();
            }
        } else {
            if ($produto->pontos > 0){
                $venda->valor -= $produto->preco_promocao;
                $venda->lucro -= $produto->preco_promocao - $produto->preco_custo;
                $venda->pontos -= round($produto->preco_promocao, 0, PHP_ROUND_HALF_DOWN);
                $venda->save();
            } else {
                $venda->valor -= $produto->preco_promocao;
                $venda->lucro -= $produto->preco_promocao - $produto->preco_custo;
                $venda->save();
            }
        }

        /* REMOVE O ITEM DO CARRINHO */
        CarrinhoVenda::where("vendas_id", "=", $vendas_id)->where("produtos_id", "=", $produto->id)->take(1)->delete();

        $success = "Produto removido com sucesso";
        session()->flash("success", $success);

        return redirect()->back();
    }

    public function concluir_venda(Request $request){
        
        $concluir = $request->all();
        $venda_temp = Venda::where("id", "=", $concluir["vendas_id"])->first();
        $cliente = User::where("cpf", $venda_temp->cpf_cliente)->first();
        $carrinho = CarrinhoVenda::where("vendas_id", $concluir["vendas_id"])->get()->toArray();
        $taxa_pagamento = FormaPagamento::where("id", $concluir["pagamento"])->first();
        $total_taxado = $venda_temp->valor - ($venda_temp->valor / 100 * $taxa_pagamento->taxa);

        if($venda_temp->valor == null || $venda_temp->valor == "" || $venda_temp->valor == 0){
            $error = "Sem produtos na venda, adicione...";
            session()->flash("alerta", $error);
            return redirect()->back();
        }

        if(!isset($concluir["pagamento"])){
            $error = "Informe o tipo de pagamento";
            session()->flash("alerta", $error);
            return redirect()->back();
        }

        if($concluir["pagamento"] == "4" || $concluir["pagamento"] == "1"){ /* PAGAMENTO EM DINHEIRO OU PIX */

            if ($venda_temp->cpf_cliente != null){  
                if($cliente != null){
                    $cliente->pontos += $cliente->pontos + $venda_temp->pontos;
                    $cliente->save();
                }
            }

            /* ADICIONA A TABELA PRODUTOVENDA OS ITENS QUE TAVA NO CARRINHO */
            $produtos_venda = new ProdutosVenda();
            for($i = 0; $i < count($carrinho); $i++){
                $produtos_venda->create($carrinho[$i]);
            }
            
            /* CRIA NOVA MOVIMENTAÇÃO FINANCEIRA */
            $nova_movimentacao = new MovimentacoesFinanceira;
            $nova_movimentacao["ponto_partida"] = "PDV";
            $nova_movimentacao["cliente_fornecedor"] = ($venda_temp->cpf_cliente === null) ? "Não informado" : $venda_temp->cpf_cliente;
            $nova_movimentacao["valor"] = $venda_temp->valor;
            $nova_movimentacao["lucro"] = $venda_temp->lucro;
            $nova_movimentacao["forma_pagamentos_id"] = $concluir["pagamento"];
            $nova_movimentacao["tipo"] = "e";
            $nova_movimentacao["data"] = now();
            $nova_movimentacao->save();

            /* APAGA TODOS OS ITENS DA VENDA QUE TAVA NO CARRINHO */
            CarrinhoVenda::where("vendas_id", $concluir["vendas_id"])->delete();

            /* FINALIZA A VENDA */
            $venda_temp->forma_pagamentos_id = $concluir["pagamento"];
            $venda_temp->status = "f";
            $venda_temp->dinheiro = $concluir["dinheiro"];
            $venda_temp->troco = $concluir["troco"];
            $venda_temp->save();

            $success = "Venda realizada com sucesso";
            session()->flash("success", $success);

            return redirect("vendedor/pdv");

        } else { /* PAGAMENTO EM CARTÃO */

            if($venda_temp->valor == null || $venda_temp->valor == "" || $venda_temp->valor == 0){
                $error = "Sem produtos na venda, adicione...";
                session()->flash("alerta", $error);
                return redirect()->back();
            }

            if ($venda_temp->cpf_cliente != null){
                if($cliente != null || $cliente != false){
                    $cliente->pontos = $cliente->pontos + $venda_temp->pontos;
                    $cliente->save();
                }
            }

            /* CRIANDO NOVA MOVIMENTAÇÃO FINANCEIRA */
            $nova_movimentacao = new MovimentacoesFinanceira;
            $nova_movimentacao["ponto_partida"] = "PDV";
            $nova_movimentacao["cliente_fornecedor"] = ($venda_temp->cpf_cliente === null) ? "Não informado" : $venda_temp->cpf_cliente;
            $nova_movimentacao["valor"] = $venda_temp->valor;
            $nova_movimentacao["lucro"] = $venda_temp->lucro - ($venda_temp->valor / 100 * $taxa_pagamento->taxa);
            $nova_movimentacao["forma_pagamentos_id"] = $concluir["pagamento"];
            $nova_movimentacao["tipo"] = "e";
            $nova_movimentacao["data"] = now();
            $nova_movimentacao->save();

            $produtos_venda = new ProdutosVenda();
            for($i = 0; $i < count($carrinho); $i++){
                $produtos_venda->create($carrinho[$i]);
            }

            /* APAGANDO ITENS DO CARRINHO */
            CarrinhoVenda::where("vendas_id", $concluir["vendas_id"])->delete();

            /* FINALIZA A VENDA */
            $venda_temp->valor = $total_taxado;
            $venda_temp->forma_pagamentos_id = $concluir["pagamento"];
            $venda_temp->status = "f";
            $venda_temp->save();

            $success = "Venda realizada com sucesso";
            session()->flash("success", $success);

            return redirect("vendedor/pdv");
            
        }
        
    }

}
