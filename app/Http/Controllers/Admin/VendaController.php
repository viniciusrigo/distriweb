<?php

namespace App\Http\Controllers\Admin;

use App\Models\FluxoCaixa;
use App\Models\ItemVenda;
use App\Models\ProdutosVenda;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Caixa;
use App\Models\CarrinhoVenda;
use App\Models\FluxoBanco;
use App\Models\FormaPagamento;
use App\Models\Lote;
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
        $caixa_aberto = Caixa::where('status', 'a')->first();
        if (!isset($caixa_aberto)){

            $alert = "Abra o caixa antes de começar as vendas";
            session()->flash("alert", $alert);

            return redirect()->route("admin.caixa.index");
        }

        /* VERIFICA SE EXISTE VENDA ABERTA, CASO EXISTA REDIRECIONA PARA VENDA ABERTA */
        $venda = Venda::where("status", "=", "a")->get();
        if(count($venda) > 0){
            return redirect("admin/pdv/venda/".$venda[0]->id);
        }
        
        return view("site/admin/pdv/index");
    }

    public function create(Request $request){

        $nova_venda = $request->all();
        
        if(count(Venda::where("data_venda", "=", $nova_venda["data_venda"])->get()) > 0){

            $venda = Venda::where("data_venda", "=", $nova_venda["data_venda"])->get();

            return redirect("admin/pdv/venda/".$venda[0]->id);
        } else{

            Venda::create($nova_venda); 

            $venda = Venda::where("data_venda", "=", $nova_venda["data_venda"])->get();
            
            return redirect("admin/pdv/venda/".$venda[0]->id);
        }
    }

    public function delete(Request $request){
        $id = $request->except('_token');
        Venda::where("id", $id)->first()->delete();

        return redirect()->route('admin.pdv.index');
    }

    public function venda(){

        $venda = Venda::where("status", "=", "a")->get();

        $qtd = count($venda);
        if ($qtd > 0) {
            $carrinho = CarrinhoVenda::where("vendas_id","=", $venda[0]->id)->join("produtos", "carrinho_vendas.produtos_id", "=", "produtos.id")
                                                                        ->select("produtos.nome", "produtos.preco", "produtos.preco_promocao", "produtos.promocao", "produtos.codigo_barras", "carrinho_vendas.data_adicao")
                                                                        ->get();
            
            return view("site/admin/pdv/venda", compact("venda", "carrinho"));

        } else {
            return redirect("admin/pdv");
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
        if(Produto::where("codigo_barras", $codigo_barras)->first() == []){

            $error = "Produto não encontrado, verifique se foi cadastrado";
            session()->flash("error", $error);

            return redirect()->back();
        }

        /* VERIFICA SE TEM O PRODUTO NO ESTOQUE */
        if ($produto->quantidade == 0) {
            //dd($produto);
            $lote = Lote::where("codigo_barras", $codigo_barras)->orderBy("data_cadastro", "asc")->first();
            if(isset($lote)){
                $produto->quantidade = $lote->quantidade;
                $produto->preco = $lote->preco;
                $produto->preco_custo = $lote->preco_custo;
                $produto->preco_promocao = $lote->preco_promocao;
                $produto->validade = $lote->validade;
                $produto->data_cadastro = $lote->data_cadastro;
                $produto->save();

                $lote->delete();
            } else {
                $error = "Produto indisponível, verifique o(s) Estoque/Lotes";
                session()->flash("error", $error);
    
                return redirect()->back();
            }
        
        }
        
        /* ATUALIZA A QUANTIDADE DO PRODUTO EM ESTOQUE */
        $produto->quantidade -= 1;
        $produto->ult_compra = now(); 
        $produto->save();

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
        $produto->quantidade += 1;
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

        /* VERIFICA SE FOI SELECIONADO O PAGAMENTO */
        if($request->input("pagamento") == null){
            $error = "Informe a forma de pagamento";
            session()->flash("error", $error);
            return redirect()->back();
        }
        
        $dados = $request->except('_token');
        
        $venda_temp = Venda::join('local_vendas', 'vendas.local_id', '=', 'local_vendas.id')->select(
            'vendas.*',
            'local_vendas.credito_id as local_credito',
            'local_vendas.debito_id as local_debito'
        )->where("vendas.id", "=", $dados["vendas_id"])->first();
        $cliente = User::where("cpf", $venda_temp->cpf_cliente)->first();
        $carrinho = CarrinhoVenda::where("vendas_id", $dados["vendas_id"])->get()->toArray();
        $caixa_aberto = Caixa::where('status', 'a')->first("id");

        if($dados["pagamento"] == 'credito'){
            $venda_temp->forma_pagamentos_id = $venda_temp->local_credito;
        } else if ($dados["pagamento"] == 'debito') {
            $venda_temp->forma_pagamentos_id = $venda_temp->local_debito;
        } else {
            $venda_temp->forma_pagamentos_id = $dados["pagamento"];
        } 
        
        $taxa_pagamento = FormaPagamento::where("id", $venda_temp->forma_pagamentos_id)->first();
        $banco = Banco::where("id", $taxa_pagamento->banco_id)->first();

        /* VERIFICA SE TEM PRODUTOS NO CARRINHO DA VENDA */
        if($venda_temp->valor == null || $venda_temp->valor == "" || $venda_temp->valor == 0){
            $error = "Sem produtos na venda, adicione...";
            session()->flash("alerta", $error);
            return redirect()->back();
        }

        /* VERIFICA SE O CLIENTE FOI INFORMADO PARA ADICIONAR PONTOS A ELE */
        if ($venda_temp->cpf_cliente != null){  
            if($cliente != null){
                $cliente->pontos += $venda_temp->pontos;  
            }
        }
        
        $venda_temp->status = "f";

        if($dados["pagamento"] == "2"){ /* PAGAMENTO EM DINHEIRO*/

            /* NOVO FLUXO DE CAIXA */
            $novo_fluxo = new FluxoCaixa();
            $novo_fluxo["caixas_id"] = $caixa_aberto->id;
            $novo_fluxo["venda"] = $dados["dinheiro"] - $dados["troco"];
            $novo_fluxo["dinheiro"] = $dados["dinheiro"];
            $novo_fluxo["troco"] = $dados["troco"];
            $novo_fluxo["data"] = now();       
            $novo_fluxo->save();

            $nova_mov = new MovimentacoesFinanceira;
            $nova_mov["local_id"] = 4;
            $nova_mov["cliente_fornecedor"] = ($venda_temp->cpf_cliente === null) ? "Não informado" : $venda_temp->cpf_cliente;
            $nova_mov["valor"] = $venda_temp->valor;
            $nova_mov["lucro"] = $venda_temp->lucro;
            $nova_mov["forma_pagamentos_id"] = $venda_temp->forma_pagamentos_id;
            $nova_mov["tipo"] = "e";
            $nova_mov["data"] = now();
            $nova_mov->save();

            $venda_temp->dinheiro = $dados["dinheiro"];
            $venda_temp->troco = $dados["troco"];

            $banco->saldo += $venda_temp->valor;
            $banco->save();            
        } 
        
        if ($dados["pagamento"] > "2") { /* PAGAMENTO EM CARTÃO OU PIX */
            
            
            $nova_mov = new MovimentacoesFinanceira;
            $nova_mov["local_id"] = 4;
            $nova_mov["cliente_fornecedor"] = ($venda_temp->cpf_cliente === null) ? "Não informado" : $venda_temp->cpf_cliente;
            $nova_mov["valor"] = $venda_temp->valor - ($venda_temp->valor / 100 * $taxa_pagamento->taxa);
            $nova_mov["lucro"] = $venda_temp->lucro - ($venda_temp->valor / 100 * $taxa_pagamento->taxa);
            $nova_mov["forma_pagamentos_id"] = $venda_temp->forma_pagamentos_id;
            $nova_mov["tipo"] = "e";
            $nova_mov["data"] = now();
            $nova_mov->save();
            
            $venda_temp->valor = $venda_temp->valor - ($venda_temp->valor / 100 * $taxa_pagamento->taxa);
            $venda_temp->taxa = $taxa_pagamento->taxa;

            $banco->saldo += $venda_temp->valor;
            $banco->save();
        }

        $cliente->save();
        $venda_temp->save();

        /* ADICIONA A TABELA PRODUTOVENDA OS ITENS QUE TAVA NO CARRINHO */
        $produtos_venda = new ProdutosVenda();
        for($i = 0; $i < count($carrinho); $i++){
            $produtos_venda->create($carrinho[$i]);
        }

        /* APAGA TODOS OS ITENS DA VENDA QUE TAVA NO CARRINHO */
        CarrinhoVenda::where("vendas_id", $dados["vendas_id"])->delete();

        $fluxo_banco = new FluxoBanco();
        $fluxo_banco->local_id = $banco->id;
        $fluxo_banco->valor = $nova_mov["valor"];
        $fluxo_banco->tipo = "e";
        $fluxo_banco->data = now();
        $fluxo_banco->save();
        
        $success = "Venda realizada com sucesso";
        session()->flash("success", $success);

        return redirect("admin/pdv");
    }

    public function index_vendas(){
        $vendas = Venda::where("status", "f")
        ->join('forma_pagamentos', 'vendas.forma_pagamentos_id', '=', 'forma_pagamentos.id')
        ->join('local_vendas', 'vendas.local_id', '=', 'local_vendas.id')
        ->select(
            'vendas.*',
            'forma_pagamentos.nome as pagamento_nome',
            'local_vendas.local'
        )->get();
        //dd($vendas);
        return view("site/admin/vendas/index", compact('vendas'));
    }

    public function detalhe_venda(string|int $id){
        $venda = Venda::find($id);
        $produtos = ProdutosVenda::where('vendas_id', $id)->join("produtos", "produtos_vendas.produtos_id", "=", "produtos.id")->select(
            "produtos.nome", "produtos.codigo_barras", "produtos.preco"
        )->get()->toArray();
        return view("site/admin/vendas/detalhe", compact("venda","produtos"));
    }

    public function consulta_produtos_ajax(Request $request){
        $produtos = ProdutosVenda::where('vendas_id', $request->input('id'))->join("produtos", "produtos_vendas.produtos_id", "=", "produtos.id")
            ->select(
                "produtos.nome",
                "produtos.codigo_barras",
                "produtos.preco"
            )->get()->toArray();
        return $produtos;
    }

}
