<?php

namespace App\Http\Controllers\Admin;

use App\Models\FluxoCaixa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Caixa;
use App\Models\CarrinhoVendaManual;
use App\Models\CombosProduto;
use App\Models\FluxoBanco;
use App\Models\FormaPagamento;
use App\Models\GlobalConfig;
use App\Models\LocalVenda;
use App\Models\Lote;
use App\Models\MovimentacoesFinanceira;
use App\Models\Produto;
use App\Models\ProdutosVenda;
use App\Models\VariaveisProduto;
use App\Models\Venda;
use App\Models\Zona;
use Exception;
use Illuminate\Support\Facades\DB;

class VendaManualController extends Controller
{
    public function page_index(){
        $venda = Venda::where("status", "=", "a")->where("local_id", null)->get();

        if(count($venda) > 0){
            return redirect("admin/venda-manual/venda/".$venda[0]->id);
        }

        return view("site/admin/venda-manual/index");
    }

    public function page_create(Request $request){
        $nova_venda_manual = $request->all();
        
        if(count(Venda::where("data_venda", $nova_venda_manual["data_venda"])->get()) > 0){

            $venda_manual = Venda::where("data_venda", $nova_venda_manual["data_venda"])->get();

            return redirect("admin/venda-manual/venda/".$venda_manual[0]->id);
        } else{
            Venda::create($nova_venda_manual); 

            $venda_manual = Venda::where("data_venda", $nova_venda_manual["data_venda"])->get();
            
            return redirect("admin/venda-manual/venda/".$venda_manual[0]->id);
        }
    }

    public function page_sale(){
        $venda_manual = Venda::where("status", "a")->where("local_id", null)->get();

        $produtos = VariaveisProduto::join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome as produto_nome", "variaveis_produtos.id","variaveis_produtos.variavel_nome")
        ->orderBy("produto_nome", "asc")->get();

        $zonas = Zona::all();

        $quantidade = count($venda_manual);
        if ($quantidade > 0) {
            $carrinho = CarrinhoVendaManual::
            join("variaveis_produtos", "carrinho_venda_manual.variavel_produto_id", "=", "variaveis_produtos.id")
            ->join("produtos", "carrinho_venda_manual.produto_id", "=", "produtos.id")
            ->select(
                "variaveis_produtos.id as variavel_produto_id",
                "variaveis_produtos.variavel_nome",
                "variaveis_produtos.preco",
                "variaveis_produtos.preco_promocao",
                "variaveis_produtos.promocao",
                "variaveis_produtos.codigo_barras",
                "produtos.nome as produto_nome"
            )->get();
            
            return view("site/admin/venda-manual/venda", compact("venda_manual", "carrinho", "produtos", "zonas"));

        } else {
            return redirect("admin/venda-manual");
        }
    }

    public function add_product(Request $request){
        try {
            $variavel_produto_id = $request->input("variavel_produto_id");
            $quantidade = $request->input("quantidade");
            $venda_id = $request->input("venda_id");
            $produto = VariaveisProduto::where("id", $variavel_produto_id)->first();
            $venda = Venda::where("id", $venda_id)->first();

            $produto_estoque = Produto::where("id", $produto->produto_id)->first();        
            
            /* VERIFICA SE TEM O PRODUTO NO ESTOQUE */
            /* VALIDADE NULL SIGNIFICA QUE O PRODUTO É UM COMBO(5) */
            if($produto->validade != null){
                /* FARDO_QUANTIDAE NULL SIGNIFICA QUE É UM PRODUTO */
                if ($produto->fardo_quantidade == null) {
                    if ($produto->variavel_quantidade == 0) {
                        //dd($produto);
                        $lote = Lote::where("codigo_barras", $produto->codigo_barras)->orderBy("data_cadastro", "asc")->first();
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
                            $error = "Produto esgotado.";
                            session()->flash("error", $error);
                            
                            return redirect()->back();
                        }     
                    }
                }

                if ($produto->fardo_quantidade == null) {
                    if ($produto->variavel_quantidade < $quantidade) {
                        $error = "Quantidade insuficiente, tem apenas ".$produto->variavel_quantidade." un";
                        session()->flash("error", $error);
                        
                        return redirect()->back();
                    }
                }
                
                /* ATUALIZA A QUANTIDADE DO VARIAVEL EM ESTOQUE */
                if ($produto_estoque->categoria_id == 6){ /* FARDO */
                    $fardo_variavel = VariaveisProduto::where("id", $produto_estoque->variavel_produto_id)->first();
                    $fardo_variavel->variavel_quantidade -= ($request->input("quantidade") * $produto->fardo_quantidade);
                    $fardo_variavel->save();
                } else { /* PRODUTO */
                    $produto->variavel_quantidade -= $request->input("quantidade") > 1 ? $request->input("quantidade") : 1;
                }
            }

            /* ATUALIZA A QUANTIDADE DO VARIAVEL EM ESTOQUE */
            if($produto_estoque->categoria_id == 5){ /* COMBO */
                $combo_produtos = CombosProduto::where("produto_id", $produto_estoque->id)->get(["variavel_produto_id", "combo_quantidade"]);
                for($x = 0; $x < count($combo_produtos); $x++){
                    $variavel = VariaveisProduto::where("id", $combo_produtos[$x]->variavel_produto_id)->first();
                    $variavel->variavel_quantidade -= ($request->input("quantidade") *  $combo_produtos[$x]->combo_quantidade);
                    $variavel->save();
                }
            }

            $produto->ult_compra = now(); 
            
            /* ATUALIZA PREÇO/LUCRO/PONTOS */
            if ($produto->promocao == "n"){
                if ($produto->pontos > 0){
                    for($i = 0; $i < $quantidade; $i++){
                        $venda->valor += $produto->preco;
                        $venda->lucro += $produto->lucro;
                        $venda->pontos += round($produto->preco, 0, PHP_ROUND_HALF_DOWN);
                    }
                } else {
                    for($i = 0; $i < $quantidade; $i++){
                        $venda->valor = $venda->valor + $produto->preco;
                        $venda->lucro += $produto->lucro;
                    }
                }
            } else {
                if ($produto->pontos > 0){
                    for($i = 0; $i < $quantidade; $i++){
                        $venda->valor += $produto->preco_promocao;
                        $venda->lucro += ($produto->preco_promocao - $produto->preco_custo);
                        $venda->pontos += round($produto->preco_promocao, 0, PHP_ROUND_HALF_DOWN);
                    }
                } else {
                    for($i = 0; $i < $quantidade; $i++){
                        $venda->valor += $produto->preco_promocao;
                        $venda->lucro += ($produto->preco_promocao - $produto->preco_custo);
                    }
                }
            }      
            
            /* ADICIONA PRODUTO NO CARRINHO */
            for($i = 0; $i < $quantidade; $i++){
                $carrinho = new CarrinhoVendaManual();
                $carrinho["venda_id"] = $venda_id;
                $carrinho["produto_id"] = $produto->produto_id;
                $carrinho["variavel_produto_id"] = $produto->id;
                $carrinho["data_adicao"] = now();
                $carrinho->save();
            }
            
            DB::transaction(function() use ($produto, $venda){
                $produto->save();
                $venda->save();
            });

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function remove_product(Request $request){
        try {
            $variavel_produto_id = $request->input("variavel_produto_id");
            $venda_id = $request->input("venda_id");
            $venda = Venda::where("id", "=", $venda_id)->first();
            $produto = VariaveisProduto::where("id", $variavel_produto_id)->first();
            
            $produto_estoque = Produto::where("id", $produto->produto_id)->first();

            /* ATUALIZA A QUANTIDADE DO VARIAVEL EM ESTOQUE */
            if($produto_estoque->categoria_id == 5){ /* COMBO */
                $combo_produtos = CombosProduto::where("produto_id", $produto_estoque->id)->get(["variavel_produto_id", "combo_quantidade"]);
                for($x = 0; $x < count($combo_produtos); $x++){
                    $variavel = VariaveisProduto::where("id", $combo_produtos[$x]->variavel_produto_id)->first();
                    $variavel->variavel_quantidade += $combo_produtos[$x]->combo_quantidade;
                    $variavel->save();
                }
            } else if ($produto_estoque->categoria_id == 6){ /* FARDO */
                $fardo_variavel = VariaveisProduto::where("id", $produto_estoque->variavel_produto_id)->first();
                $fardo_variavel->variavel_quantidade += $produto->fardo_quantidade;
                $fardo_variavel->save();
            } else { /* PRODUTO */
                $produto->variavel_quantidade += 1;
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
            CarrinhoVendaManual::where("venda_id", $venda_id)->where("variavel_produto_id", $produto->id)->take(1)->delete();

            DB::transaction(function() use ($produto, $venda){
                $produto->save();
                $venda->save();
            });

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function delete(Request $request){
        try {
            $id = $request->except("_token");
            Venda::where("id", $id)->first()->delete();

            return redirect()->route("admin.venda-manual.index");
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function imprimir(string|int $v, string $f, string|int $z = "", string|int $e = "", string|int $d = "", string|int $t = ""){
        $venda_id = intval($v);
        if($f == "credito"){
            $x = LocalVenda::find(4);
            $pagamento = FormaPagamento::find($x->credito_id);
        } else if($f == "debito"){
            $x = LocalVenda::find(4);
            $pagamento = FormaPagamento::find($x->debito_id);
        } else {
            $pagamento = FormaPagamento::find($f);
        }

        $destino = $e;
        $zona = Zona::find($z);

        $dinheiro = $d;
        $troco = $t;

        $venda = Venda::where("id",$venda_id)->first("valor");
        $produtos_pdv = CarrinhoVendaManual::where("venda_id", $venda_id)
        ->join("produtos", "carrinho_venda_manual.produto_id", "=", "produtos.id")
        ->join("variaveis_produtos", "carrinho_venda_manual.variavel_produto_id", "=", "variaveis_produtos.id")
        ->select("produtos.nome", "variaveis_produtos.codigo_barras", "variaveis_produtos.variavel_nome", "variaveis_produtos.preco", "variaveis_produtos.preco_promocao", "variaveis_produtos.promocao")
        ->get();

        $info_empresa = GlobalConfig::find(1)->toArray();

        return view("site/admin/venda-manual/imprimir", compact("produtos_pdv", "venda", "zona", "destino", "pagamento", "dinheiro", "troco", "info_empresa"));
    }

    public function finish(Request $request){
        try {
            $dados = $request->except("_token");
            $venda_temp = Venda::where("vendas.id", $dados["venda_id"])->first();
            $carrinho = CarrinhoVendaManual::where("venda_id", $dados["venda_id"])->get()->toArray();
            $caixa_aberto = Caixa::where("status", "a")->first("id");
            $local_venda = LocalVenda::find($dados["local_id"]);
            $zona = Zona::find($dados["zona_id"]);

            $venda_temp->cpf_cliente = "Online";
            $venda_temp->local_id = $local_venda->id;
            $venda_temp->valor += $zona->entrega;
            $venda_temp->lucro += $zona->entrega;

            if($dados["pagamento"] == "credito"){
                $venda_temp->forma_pagamento_id = $local_venda->credito_id;
            } else if ($dados["pagamento"] == "debito") {
                $venda_temp->forma_pagamento_id = $local_venda->debito_id;
            } else {
                $venda_temp->forma_pagamento_id = $dados["pagamento"];
            } 
            $taxa_pagamento = FormaPagamento::where("id", $venda_temp->forma_pagamento_id)->first();
            $banco = Banco::where("id", $taxa_pagamento->banco_id)->first(); 
            
            $venda_temp->status = "f";

            if($dados["pagamento"] == "2"){ /* PAGAMENTO EM DINHEIRO*/

                /* NOVO FLUXO DE CAIXA */
                $novo_fluxo = new FluxoCaixa();
                $novo_fluxo["caixa_id"] = $caixa_aberto->id;
                $novo_fluxo["venda"] = $venda_temp->valor;
                $novo_fluxo["dinheiro"] = $dados["dinheiro"];
                $novo_fluxo["troco"] = $dados["troco"];
                $novo_fluxo["data"] = now();       
                
                /* NOVA MOVIMENTAÇÃO FINANCEIRA */
                $nova_mov = new MovimentacoesFinanceira;
                $nova_mov["local_id"] = $local_venda->id;
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
                    $produtos_pedido = new ProdutosVenda();
                    for($i = 0; $i < count($carrinho); $i++){
                        $produtos_pedido->create($carrinho[$i]);
                    }

                    /* APAGA TODOS OS ITENS DA VENDA QUE TAVA NO CARRINHO */
                    CarrinhoVendaManual::where("venda_id", $dados["venda_id"])->delete();

                    $fluxo_banco->save();
                });
            } else { /* PAGAMENTO EM CARTÃO / TICKET / PIX */
                
                $nova_mov = new MovimentacoesFinanceira;
                $nova_mov["local_id"] = $local_venda->id;
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

                    /* ADICIONA A TABELA PRODUTOSVENDA OS ITENS QUE TAVA NO CARRINHO */
                    $produtos_pedido = new ProdutosVenda();
                    for($i = 0; $i < count($carrinho); $i++){
                        $produtos_pedido->create($carrinho[$i]);
                    }

                    /* APAGA TODOS OS ITENS DA VENDA QUE TAVA NO CARRINHO */
                    CarrinhoVendaManual::where("venda_id", $dados["venda_id"])->delete();

                    $fluxo_banco->save();

                });
            }
            
            $success = "Venda realizada com sucesso";
            session()->flash("success", $success);

            return redirect("admin/venda-manual");
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function additional(Request $request){
        try {
            $dados = $request->except("_token");
            $dados["adicional"] = str_replace(",", ".", $dados["adicional"]);
            $venda = Venda::find($dados["venda_id"]);
            $venda->valor += $dados["adicional"];
            $venda->lucro += $dados["adicional"];
            $venda->save();

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
}
