<?php

namespace App\Http\Controllers\Admin;

use App\Models\CarrinhoVenda;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Caixa;
use App\Models\CombosProduto;
use App\Models\FluxoBanco;
use App\Models\FluxoCaixa;
use App\Models\FormaPagamento;
use App\Models\GlobalConfig;
use App\Models\Lote;
use App\Models\MovimentacoesFinanceira;
use App\Models\Produto;
use App\Models\ProdutosVenda;
use App\Models\VariaveisProduto;
use App\Models\Venda;
use Exception;
use Illuminate\Support\Facades\DB;

class PDV2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function page_index(){
        $caixa_aberto = Caixa::where("status", "a")->first();
        if (!isset($caixa_aberto)){
            $error = "Abra o caixa antes de começar as vendas";
            session()->flash("error", $error);

            return redirect()->route("admin.caixa.index");
        }
        /* VERIFICA SE EXISTE VENDA ABERTA, CASO EXISTA REDIRECIONA PARA VENDA ABERTA */
        $venda = Venda::where("status", "=", "a")->where("local_id", 5)->get();
        if(count($venda) > 0){
            return redirect("admin/pdv2/venda/".$venda[0]->id);
        }
        
        return view("site/admin/pdv2/index");
    }

    public function page_create(Request $request){
        $nova_venda = $request->all();
        
        if(count(Venda::where("data_venda", $nova_venda["data_venda"])->get()) > 0){
            $venda = Venda::where("data_venda", $nova_venda["data_venda"])->get();

            return redirect("admin/pdv2/venda/".$venda[0]->id);
        } else{
            Venda::create($nova_venda); 

            $venda = Venda::where("data_venda", $nova_venda["data_venda"])->get();
            
            return redirect("admin/pdv2/venda/".$venda[0]->id);
        }
    }

    public function delete(Request $request){
        try {
            $id = $request->except("_token");
            Venda::where("id", $id)->first()->delete();

            return redirect()->route("admin.pdv2.index");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function page_sale(){
        $venda = Venda::where("status", "a")->where("local_id", 5)->get();

        $produtos = VariaveisProduto::join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome as produto_nome", "variaveis_produtos.id","variaveis_produtos.variavel_nome")
        ->orderBy("produto_nome", "asc")->get();

        $quantidade = count($venda);

        if ($quantidade > 0) {
            $carrinho = CarrinhoVenda::where("venda_id", $venda[0]->id)
            ->join("variaveis_produtos", "carrinho_vendas.variavel_produto_id", "=", "variaveis_produtos.id")
            ->join("produtos", "carrinho_vendas.produto_id", "=", "produtos.id")
            ->select(
                "variaveis_produtos.id as variavel_produto_id",
                "variaveis_produtos.variavel_nome",
                "variaveis_produtos.preco",
                "variaveis_produtos.preco_promocao",
                "variaveis_produtos.promocao",
                "variaveis_produtos.codigo_barras",
                "produtos.nome as produto_nome"
            )->get();
            
            return view("site/admin/pdv2/venda", compact("venda", "carrinho", "produtos"));
        } else {
            return redirect("admin/pdv");
        }
    }

    public function add_product(Request $request){
        try {
            $variavel_produto_id = $request->input("variavel_produto_id");
            
            if(!isset($variavel_produto_id)){
                $produto = VariaveisProduto::where("codigo_barras", $request->input("codigo_barras"))->first();
            } else {
                $produto = VariaveisProduto::where("id", $variavel_produto_id)->first();
            }
            
            $venda = Venda::where("id", $request->input("venda_id"))->first();
            $produto_estoque = Produto::where("id", $produto->produto_id)->first();
            
            /* VERIFICA SE O CÓDIGO DE BARRAS É VALIDO */
            if(!isset($variavel_produto_id)){
                if ($request->input("codigo_barras") == null || $request->input("codigo_barras") == "") {
                    $error = "Informe o código de barras";
                    session()->flash("error", $error);
                    
                    return redirect()->back();
                }
            }
            
            /* VERIFICA SE O PRODUTO EXISTE */
            if(!isset($variavel_produto_id)){
                if(VariaveisProduto::where("codigo_barras", $request->input("codigo_barras"))->first() == []){
                    
                    $error = "Produto não encontrado, verifique se foi cadastrado";
                    session()->flash("error", $error);
                    
                    return redirect()->back();
                }
            } else {
                if(VariaveisProduto::where("id", $variavel_produto_id)->first() == []){
                    $error = "Produto não encontrado, verifique se foi cadastrado";
                    session()->flash("error", $error);
                    
                    return redirect()->back();
                }
            }
            
            /* VERIFICA SE TEM O PRODUTO NO ESTOQUE */
            if($produto->validade != null){
                if ($produto->fardo_quantidade == null) {
                    if ($produto->variavel_quantidade == 0 || $produto->variavel_quantidade == null) {
                        //dd($produto);
                        /* VERIFICA SE TEM UM LOTE DA VARIÁVEL */
                        $lote = Lote::where("variavel_produto_id", $produto->id)->orderBy("data_cadastro", "asc")->first();
                        if(isset($lote)){
                            $produto->variavel_quantidade = $lote->quantidade;
                            $produto->preco = $lote->preco;
                            $produto->preco_custo = $lote->preco_custo;
                            $produto->preco_promocao = $lote->preco_promocao;
                            $produto->lucro = $produto->preco - $produto->preco_custo;
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
                }

                if ($produto->fardo_quantidade == null) {
                    if ($produto->variavel_quantidade < $request->input("quantidade")) {

                        $error = "Quantidade insuficiente, tem apenas ".$produto->variavel_quantidade." un";
                        session()->flash("error", $error);
                        
                        return redirect()->back();
                    }
                }

                /* ATUALIZA A QUANTIDADE DO VARIAVEL EM ESTOQUE */
                if ($produto_estoque->categoria_id == 6){
                    $fardo_variavel = VariaveisProduto::where("id", $produto_estoque->variavel_produto_id)->first();
                    $fardo_variavel->variavel_quantidade -= ($request->input("quantidade") * $produto->fardo_quantidade);
                    $fardo_variavel->save();
                } else {
                    $produto->variavel_quantidade -= $request->input("quantidade") > 1 ? $request->input("quantidade") : 1;
                }
            }

            /* ATUALIZA A QUANTIDADE DO VARIAVEL EM ESTOQUE */
            if($produto_estoque->categoria_id == 5){ /* COMBO */
                $combo_produtos = CombosProduto::where("produto_id", $produto_estoque->id)->get(["variavel_produto_id", "combo_quantidade"]);
                for($x = 0; $x < count($combo_produtos); $x++){
                    $variavel = VariaveisProduto::where("id", $combo_produtos[$x]->variavel_produto_id)->first();
                    $variavel->variavel_quantidade -= ($request->input("quantidade") * $combo_produtos[$x]->combo_quantidade);
                    $variavel->save();
                }
            }
            
            $produto->ult_compra = now(); 
            
            /* ATUALIZA PREÇO/LUCRO/PONTOS */
            if ($produto->promocao == "n"){
                if ($produto->pontos > 0){
                    for($i = 0; $i < $request->input("quantidade"); $i++){
                        $venda->valor += $produto->preco;
                        $venda->lucro += $produto->lucro;
                        $venda->pontos += round($produto->preco, 0, PHP_ROUND_HALF_DOWN);
                    }
                } else {
                    for($i = 0; $i < $request->input("quantidade"); $i++){
                        $venda->valor = $venda->valor + $produto->preco;
                        $venda->lucro += $produto->lucro;
                    }
                }
            } else {
                if ($produto->pontos > 0){
                    for($i = 0; $i < $request->input("quantidade"); $i++){
                        $venda->valor += $produto->preco_promocao;
                        $venda->lucro += ($produto->preco_promocao - $produto->preco_custo);
                        $venda->pontos += round($produto->preco_promocao, 0, PHP_ROUND_HALF_DOWN);
                    }
                } else {
                    for($i = 0; $i < $request->input("quantidade"); $i++){
                        $venda->valor += $produto->preco_promocao;
                        $venda->lucro += ($produto->preco_promocao - $produto->preco_custo);
                    }
                }
            }      
            
            /* ADICIONA PRODUTO NO CARRINHO */
            for($i = 0; $i < $request->input("quantidade"); $i++){
                $carrinho = new CarrinhoVenda();
                $carrinho["local_id"] = $venda->local_id;
                $carrinho["venda_id"] = $request->input("venda_id");
                $carrinho["produto_id"] = $produto->produto_id;
                $carrinho["variavel_produto_id"] = $produto->id;
                $carrinho["data_adicao"] = now();
                $carrinho->save();
            }
            
            DB::transaction(function() use ($produto, $venda){
                $produto->save();
                $venda->save();
            });
            
            session()->flash("produto", true);
            session()->flash("nome", $produto_estoque->nome);
            session()->flash("codigo_barras", $produto->codigo_barras);
            if($produto->promocao == "s"){
                session()->flash("preco", $produto->preco_promocao);
            } else {
                session()->flash("preco", $produto->preco);
            }
            session()->flash("id", $produto->id);
            
            return redirect()->back()->with(["produto" => $produto, "produto_estoque" => $produto_estoque]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function remove_product(Request $request){
        try {
            $codigo_barras_remover = $request->input("codigo_barras_remover");
            $variavel_produto_id = $request->input("variavel_produto_id");
            $venda_id = $request->input("venda_id");
            $venda = Venda::where("id", "=", $venda_id)->first();

            if(!isset($variavel_produto_id)){
                $produto = VariaveisProduto::where("codigo_barras", $codigo_barras_remover)->first();
            } else {
                $produto = VariaveisProduto::where("id", $variavel_produto_id)->first();
            }

            $produto_estoque = Produto::where("id", $produto->produto_id)->first();

            if(!isset($variavel_produto_id)){
                if ($codigo_barras_remover == null || $codigo_barras_remover == "") {
                    $error = "Informe o código de barras";
                    session()->flash("alerta", $error);
                    return redirect()->back();
                }
                if(VariaveisProduto::where("codigo_barras", $codigo_barras_remover)->first()->toArray() === []){
                    $error = "Produto não encontrado";
                    session()->flash("error", $error);
                    return redirect()->back();
                }
            } else {
                if (CarrinhoVenda::where("venda_id", "=", $venda_id)->where("variavel_produto_id", "=", $produto->id)->take(1)->get()->toArray() === []) {
                    $error = "O produto informado no momento não está no carrinho";
                    session()->flash("error", $error);
                    return redirect()->back();
                }
            }

            if($produto->validade != null){
                /* ATUALIZA ESTOQUE DO PRODUTO */
                if ($produto_estoque->categoria_id == 6){
                    $fardo_variavel = VariaveisProduto::where("id", $produto_estoque->variavel_produto_id)->first();
                    $fardo_variavel->variavel_quantidade += $produto->fardo_quantidade;
                    $fardo_variavel->save();
                } else {
                    $produto->variavel_quantidade += 1;
                }
            }

            /* ATUALIZA A QUANTIDADE DO VARIAVEL EM ESTOQUE */
            if($produto_estoque->categoria_id == 5){ /* COMBO */
                $combo_produtos = CombosProduto::where("produto_id", $produto_estoque->id)->get(["variavel_produto_id", "combo_quantidade"]);
                for($x = 0; $x < count($combo_produtos); $x++){
                    $variavel = VariaveisProduto::where("id", $combo_produtos[$x]->variavel_produto_id)->first();
                    $variavel->variavel_quantidade += $combo_produtos[$x]->combo_quantidade;
                    $variavel->save();
                }
            }

            /* ATUALIZA VALOR TOTAL DA COMPRA */
            if ($produto->promocao == "n"){
                if ($produto->pontos > 0){
                    $venda->valor -= $produto->preco;
                    $venda->lucro -= $produto->lucro;
                    $venda->pontos -= round($produto->preco, 0, PHP_ROUND_HALF_DOWN);
                } else {
                    $venda->valor -= $produto->preco;
                    $venda->lucro -= $produto->lucro;
                }
            } else {
                if ($produto->pontos > 0){
                    $venda->valor -= $produto->preco_promocao;
                    $venda->lucro -= $produto->preco_promocao - $produto->preco_custo;
                    $venda->pontos -= round($produto->preco_promocao, 0, PHP_ROUND_HALF_DOWN);
                } else {
                    $venda->valor -= $produto->preco_promocao;
                    $venda->lucro -= $produto->preco_promocao - $produto->preco_custo;
                }
            }

            /* REMOVE O ITEM DO CARRINHO */
            
            DB::transaction(function() use ($produto, $venda, $venda_id){
                $produto->save();
                $venda->save();
                CarrinhoVenda::where("venda_id", $venda_id)->where("variavel_produto_id", $produto->id)->take(1)->delete();
            });

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function finish_sale(Request $request){
        try {
            /* VERIFICA SE FOI SELECIONADO O PAGAMENTO */
            if($request->input("pagamento") == null){
                $error = "Informe a forma de pagamento";
                session()->flash("error", $error);
                return redirect()->back();
            }
            
            $dados = $request->except("_token");
            
            $venda_temp = Venda::join("local_vendas", "vendas.local_id", "=", "local_vendas.id")->select(
                "vendas.*",
                "local_vendas.credito_id as local_credito",
                "local_vendas.debito_id as local_debito"
            )->where("vendas.id", "=", $dados["venda_id"])->first();
            $carrinho = CarrinhoVenda::where("venda_id", $dados["venda_id"])->get()->toArray();
            $caixa_aberto = Caixa::where("status", "a")->first("id");

            if($dados["pagamento"] == "credito" || $dados["pagamento"] == "debito"){
                $venda_temp->forma_pagamento_id = $dados["pagamento"] == "credito" ? $venda_temp->local_credito : $venda_temp->local_debito ;
                $taxa_pagamento = FormaPagamento::where("id", $venda_temp->forma_pagamento_id)->first();
            } else if ($dados["pagamento"] == "Ticket") {
                $taxa_pagamento = FormaPagamento::where("nome", "Ticket")->first();
                $venda_temp->forma_pagamento_id = $taxa_pagamento->id;
            } else {
                $venda_temp->forma_pagamento_id = $dados["pagamento"];
                $taxa_pagamento = FormaPagamento::where("id", $venda_temp->forma_pagamento_id)->first();
            } 
            
            $banco = Banco::where("id", $taxa_pagamento->banco_id)->first();

            /* VERIFICA SE TEM PRODUTOS NO CARRINHO DA VENDA */
            if($venda_temp->valor == null || $venda_temp->valor == "" || $venda_temp->valor == 0){
                $error = "Sem produtos na venda, adicione...";
                session()->flash("alerta", $error);
                return redirect()->back();
            }
            
            $venda_temp->status = "f";

            if($dados["pagamento"] == "2"){ /* PAGAMENTO EM DINHEIRO*/

                /* NOVO FLUXO DE CAIXA */
                $novo_fluxo = new FluxoCaixa();
                $novo_fluxo["caixa_id"] = $caixa_aberto->id;
                $novo_fluxo["venda"] = $dados["dinheiro"] - $dados["troco"];
                $novo_fluxo["dinheiro"] = $dados["dinheiro"];
                $novo_fluxo["troco"] = $dados["troco"];
                $novo_fluxo["data"] = now();       
                
                $nova_mov = new MovimentacoesFinanceira;
                $nova_mov["local_id"] = $venda_temp->local_id;
                $nova_mov["cliente_fornecedor"] = ($venda_temp->cpf_cliente === null) ? "Não informado" : $venda_temp->cpf_cliente;
                $nova_mov["valor"] = $venda_temp->valor;
                $nova_mov["lucro"] = $venda_temp->lucro;
                $nova_mov["forma_pagamento_id"] = $venda_temp->forma_pagamento_id;
                $nova_mov["tipo"] = "e";
                $nova_mov["data"] = now();
                
                $venda_temp->dinheiro = $dados["dinheiro"];
                $venda_temp->troco = $dados["troco"];
                
                $banco->saldo += $venda_temp->valor;

                $fluxo_banco = new FluxoBanco();
                $fluxo_banco->banco_id = $banco->id;
                $fluxo_banco->valor = $nova_mov["valor"];
                $fluxo_banco->tipo = "e";
                $fluxo_banco->data = now();

                DB::transaction(function() use ($novo_fluxo, $nova_mov, $venda_temp, $banco, $carrinho, $dados, $fluxo_banco){

                    $novo_fluxo->save();
                    $nova_mov->save();
                    $venda_temp->save();
                    $banco->save();

                    /* ADICIONA A TABELA PRODUTOVENDA OS ITENS QUE TAVA NO CARRINHO */
                    $produtos_venda = new ProdutosVenda();
                    for($i = 0; $i < count($carrinho); $i++){
                        $produtos_venda->create($carrinho[$i]);
                    }

                    /* APAGA TODOS OS ITENS DA VENDA QUE TAVA NO CARRINHO */
                    CarrinhoVenda::where("venda_id", $dados["venda_id"])->delete();

                    $fluxo_banco->save();
                });
            } else { /* PAGAMENTO EM CARTÃO/TICKET/PIX */
                
                $nova_mov = new MovimentacoesFinanceira;
                $nova_mov["local_id"] = $venda_temp->local_id;
                $nova_mov["cliente_fornecedor"] = ($venda_temp->cpf_cliente === null) ? "Não informado" : $venda_temp->cpf_cliente;
                $nova_mov["valor"] = $venda_temp->valor - ($venda_temp->valor / 100 * $taxa_pagamento->taxa);
                $nova_mov["lucro"] = $venda_temp->lucro - ($venda_temp->valor / 100 * $taxa_pagamento->taxa);
                $nova_mov["forma_pagamento_id"] = $venda_temp->forma_pagamento_id;
                $nova_mov["tipo"] = "e";
                $nova_mov["data"] = now();
                
                $venda_temp->valor = $venda_temp->valor - ($venda_temp->valor / 100 * $taxa_pagamento->taxa);
                $venda_temp->taxa = $taxa_pagamento->taxa;

                $banco->saldo += $venda_temp->valor;

                $fluxo_banco = new FluxoBanco();
                $fluxo_banco->banco_id = $banco->id;
                $fluxo_banco->valor = $nova_mov["valor"];
                $fluxo_banco->tipo = "e";
                $fluxo_banco->data = now();

                DB::transaction(function() use ($nova_mov, $venda_temp, $banco, $carrinho, $dados, $fluxo_banco){

                    $nova_mov->save();
                    $venda_temp->save();
                    $banco->save();

                    /* ADICIONA A TABELA PRODUTOVENDA OS ITENS QUE TAVA NO CARRINHO */
                    $produtos_venda = new ProdutosVenda();
                    for($i = 0; $i < count($carrinho); $i++){
                        $produtos_venda->create($carrinho[$i]);
                    }

                    /* APAGA TODOS OS ITENS DA VENDA QUE TAVA NO CARRINHO */
                    CarrinhoVenda::where("venda_id", $dados["venda_id"])->delete();

                    $fluxo_banco->save();

                });
            }

            $success = "Venda realizada com sucesso";
            session()->flash("success", $success);

            return redirect("admin/pdv2");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function consult_products_ajax(Request $request){
        $produtos = ProdutosVenda::where("venda_id", $request->input("id"))->join("produtos", "produtos_vendas.produto_id", "=", "produtos.id")
            ->select(
                "produtos.nome",
                "produtos.codigo_barras",
                "produtos.preco"
            )->get()->toArray();
        return $produtos;
    }

    public function imprimir_venda(string|int $id){
        $numero = intval($id);

        $venda = Venda::where("id",$numero)->first("valor");
        $produtos_pdv = CarrinhoVenda::where("venda_id", $numero)
        ->join("produtos", "carrinho_vendas.produto_id", "=", "produtos.id")
        ->join("variaveis_produtos", "carrinho_vendas.variavel_produto_id", "=", "variaveis_produtos.id")
        ->select("produtos.nome", "variaveis_produtos.codigo_barras", "variaveis_produtos.variavel_nome", "variaveis_produtos.preco", "variaveis_produtos.preco_promocao", "variaveis_produtos.promocao")
        ->get();

        $info_empresa = GlobalConfig::find(1)->toArray();

        return view("site/admin/pdv2/imprimir", compact("produtos_pdv", "venda", "info_empresa"));
    }

    public function additional_sale(Request $request){
        try {
            $dados = $request->except("_token");
            $dados["adicional"] = str_replace(",", ".", $dados["adicional"]);
            $venda = Venda::find($dados["venda_id"]);
            $venda->valor += $dados["adicional"];
            $venda->lucro += $dados["adicional"];
            $venda->save();

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
