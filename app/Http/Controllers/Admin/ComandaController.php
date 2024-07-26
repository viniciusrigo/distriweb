<?php

namespace App\Http\Controllers\Admin;

use App\Models\FluxoBanco;
use App\Models\ProdutosComanda;
use App\Models\Venda;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Caixa;
use App\Models\CarrinhoComanda;
use App\Models\Comanda;
use App\Models\FluxoCaixa;
use App\Models\FormaPagamento;
use App\Models\LocalVenda;
use App\Models\Lote;
use App\Models\MovimentacoesFinanceira;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\VariaveisProduto;
use Illuminate\Support\Facades\DB;
use Exception;

class ComandaController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function page_index(Comanda $comanda){
        $comandas = DB::table("comandas")
            ->where("status", "a")
            ->get();

         $comandas_fechadas = DB::table("comandas")->where("status", "f")
            ->join("forma_pagamentos", "comandas.forma_pagamento_id", "=","forma_pagamentos.id")
            ->select(
                "comandas.id",
                "comandas.nome",
                "comandas.total",
                "comandas.lucro",
                "comandas.forma_pagamento_id",
                "forma_pagamentos.nome as pagamento_nome",
                "comandas.dinheiro",
                "comandas.troco",
                "comandas.status",
                "comandas.data_abertura",
                "comandas.data_fechamento"
            )->take(100)
            ->get();

        for($i= 0;$i<count($comandas);$i++) {
            $comandaProduto = DB::table("carrinho_comandas")->where("comanda_id", "=", $comandas[$i]->id)
            ->join("produtos", "carrinho_comandas.produto_id", "=","produtos.id")
            ->join("variaveis_produtos", "carrinho_comandas.variavel_produto_id", "=","variaveis_produtos.id")
            ->select(
                "carrinho_comandas.id",
                "carrinho_comandas.data",
                "produtos.nome as produto_nome",
                "variaveis_produtos.variavel_nome",
                "variaveis_produtos.preco",
                "variaveis_produtos.preco_promocao",
                "variaveis_produtos.promocao"
            )->get();

            $comandas[$i]->produtos = $comandaProduto;
            $comandas[$i]->data_abertura = date("H:i:s", strtotime($comandas[$i]->data_abertura));
            $comandas[$i]->data_fechamento = date("H:i:s", strtotime($comandas[$i]->data_fechamento));
        }

        $variaveis_produtos = VariaveisProduto::join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("variaveis_produtos.id", "produtos.nome as produto_nome", "variaveis_produtos.variavel_nome", "produtos.categoria_id")
        ->where("categoria_id", "!=", 5)
        ->where("categoria_id", "!=", 6)
        ->orderBy("produto_nome", "asc")->get();

        return view("site/admin/comandas/index", compact("comandas", "comandas_fechadas", "variaveis_produtos"));
    }

    public function store(Request $request, Comanda $comanda){
        try{
            $caixa_aberto = Caixa::where("status", "a")->first();
            if (!isset($caixa_aberto)){

                $error = "Abra o caixa antes de começar as vendas";
                session()->flash("error", $error);

                return redirect()->route("admin.caixa.index");
            }

            $dados = $request->all();
            $dados["data_abertura"] = now()->format("Y-m-d H:i:s");
            $comanda->create($dados);

            $success = "Comanda aberta com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function destroy(string $id) {
        try{
            $comanda = Comanda::find($id);

            $comanda->delete();

            $success = "Comanda excluída com sucesso";
            session()->flash("success", $success);

            return redirect()->route("admin.comandas.index");
        } catch (Exception $e) {
            //dd($e);
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function add_product(Request $request, VariaveisProduto $produto) {
        try{
            $dados_request = $request->except("_token");
            /* PEGANDO O PRODUTO DO ESTOQUE */
            if(!isset($dados_request["variavel_produto_id"])){
                $variavel_produto = $produto->where("codigo_barras", $dados_request["codigo_barras"])->first();
            } else {
                $variavel_produto = $produto->where("id", $dados_request["variavel_produto_id"])->first();
            }

            if ($variavel_produto->variavel_quantidade == 0) {
                if(!isset($$dados_request["variavel_produto_id"])){
                    $lote = Lote::where("codigo_barras", $dados_request["codigo_barras"])->orderBy("data_cadastro", "asc")->first();
                } else {
                    $lote = Lote::where("variavel_produto_id", $dados_request["variavel_produto_id"])->orderBy("data_cadastro", "asc")->first();
                }
                
                if(isset($lote)){
                    $variavel_produto->variavel_quantidade = $lote->quantidade;
                    $variavel_produto->preco = $lote->preco;
                    $variavel_produto->preco_custo = $lote->preco_custo;
                    $variavel_produto->preco_promocao = $lote->preco_promocao;
                    $variavel_produto->lucro = $variavel_produto->preco - $variavel_produto->preco_custo;
                    $variavel_produto->validade = $lote->validade;
                    $variavel_produto->data_cadastro = $lote->data_cadastro;
                    try{
                        $lote->delete();
                    } catch (Exception $e) {
                        return redirect()->back()->with("error", $e->getMessage());
                    }
                } else {
                    $error = "Produto indisponível, verifique o(s) Estoque/Lotes";
                    session()->flash("error", $error);
        
                    return redirect()->back();
                }
            }
            $variavel_produto->variavel_quantidade -= 1;
            $variavel_produto->ult_compra = now(); 
            
            /* ADICONANDO PRODUTO AO CARRINHO DA COMANDA */
            $carrinho = new CarrinhoComanda;
            $carrinho->comanda_id = $dados_request["comanda_id"];
            $carrinho->produto_id = $variavel_produto->produto_id;
            $carrinho->variavel_produto_id = $variavel_produto->id;
            $carrinho->data = now();
            
            
            /* ATUALIZANDO VALOR TOTAL E LUCRO */
            $comanda = Comanda::find($dados_request["comanda_id"]);
            $comanda["total"] = $comanda["total"] + ($variavel_produto->promocao == "n" ? $variavel_produto->preco : $variavel_produto->preco_promocao);
            $comanda["lucro"] += $variavel_produto->lucro;
            
            DB::transaction(function() use ($variavel_produto, $carrinho, $comanda){
                $variavel_produto->save();
                $carrinho->save();
                $comanda->save();
            });

            $success = "Produto adicionado com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function remove_product(Request $resquest) {

        try {
            $produto_request = $resquest->all();
            $produto_carrinho = CarrinhoComanda::find($produto_request["variavel_produto_id"]);
            $comanda = Comanda::find($produto_request["comanda_id"]);
            $variavel_produto = VariaveisProduto::find($produto_carrinho["variavel_produto_id"]);

            /* ATUALIZANDO VALOR DA COMANDA */
            $comanda["total"] -= $variavel_produto["preco"];
            $comanda["lucro"] -= $variavel_produto["lucro"];
            
            /* ATUALIZANDO A QUANTIDADE EM ESTOQUE */
            $variavel_produto["variavel_quantidade"] += 1;
            
            DB::transaction(function() use ($comanda, $variavel_produto, $produto_carrinho){
                $comanda->save();
                $variavel_produto->save();
                /* REMOVENDO PRODUTO DO CARRINHO DA COMANDA */
                $produto_carrinho->delete();
            });

            $success = "Produto removido com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }    
    }

    public function closed(Request $request) {
        try {
            $comanda = Comanda::find($request->comanda_id);
            $local = LocalVenda::find(3);
            $carrinho = CarrinhoComanda::where("comanda_id", $request->comanda_id)->get()->toArray();
            if($request["pagamento"] == "credito"){
                $comanda->forma_pagamento_id = $local->credito_id;
            } else if ($request["pagamento"] == "debito") {
                $comanda->forma_pagamento_id = $local->debito_id;
            } else {
                $comanda->forma_pagamento_id = $request["pagamento"];
            }
            $nova_mov = new MovimentacoesFinanceira;
            $nova_venda = new Venda();
            $taxa_pagamento = FormaPagamento::where("id", $comanda->forma_pagamento_id)->first();
            
            /* REALIZAÇÃO DA TAXAÇÃO DO VALOR TOTAL */
            if ($comanda->forma_pagamento_id > "3") {
                $comanda->total = $comanda->total - ($comanda->total / 100 * $taxa_pagamento->taxa);
                $comanda->lucro -= round(($comanda->total * 100) / (100 - $taxa_pagamento->taxa) - $comanda->total, 2);
                $comanda->taxa = $taxa_pagamento->taxa;
            }
            
            $banco = Banco::where("id", $taxa_pagamento->banco_id)->first();

            $comanda->dinheiro = $request["dinheiro"];
            $comanda->troco = $request["troco"];
            $comanda->status = "f";
            $comanda->data_fechamento = now();
            
            $novo_fluxo = "";

            if($request["pagamento"] == "2"){

                /* NOVO FLUXO */
                $caixa_aberto = Caixa::where("status", "a")->first("id");
                $novo_fluxo = new FluxoCaixa();
                $novo_fluxo["caixa_id"] = $caixa_aberto->id;
                $novo_fluxo["venda"] = $comanda->total;
                $novo_fluxo["dinheiro"] = $request->dinheiro;
                $novo_fluxo["troco"] = $request->troco;
                $novo_fluxo["data"] = now();
                
            }

            /* NOVA MOVIMENTAÇÃO */
            $nova_mov["local_id"] = 3;
            $nova_mov["cliente_fornecedor"] = $comanda->nome;
            $nova_mov["valor"] = $comanda->total;
            $nova_mov["lucro"] = $comanda->lucro;
            $nova_mov["forma_pagamento_id"] = $comanda->forma_pagamento_id;
            $nova_mov["tipo"] = "e";
            $nova_mov["data"] = $comanda->data_fechamento;

            /* NOVA VENDA */
            $nova_venda["local_id"] = 3;
            $nova_venda["valor"] = $comanda->total;
            $nova_venda["lucro"] = $comanda->lucro;
            $nova_venda["comanda_id"] = $comanda->id;
            $nova_venda["forma_pagamento_id"] = $comanda->forma_pagamento_id;
            $nova_venda["taxa"] = $taxa_pagamento->taxa;
            $nova_venda["dinheiro"] = $comanda->dinheiro;
            $nova_venda["troco"] = $comanda->troco;
            $nova_venda["status"] = "f";
            $nova_venda["data_venda"] = $comanda->data_fechamento;

            $fluxo_banco = new FluxoBanco();
            $fluxo_banco->banco_id = $banco->id;
            $fluxo_banco->valor = $nova_venda["valor"];
            $fluxo_banco->tipo = "e";
            $fluxo_banco->data = now();
            
            $banco->saldo += $comanda->total;

            DB::transaction(function() use ($request, $novo_fluxo, $comanda, $nova_mov, $nova_venda, $fluxo_banco, $banco, $carrinho){
                if($request["pagamento"] == "2"){
                    $novo_fluxo->save();
                }
                $comanda->save();
                $nova_mov->save();
                $nova_venda->save();
                $fluxo_banco->save(); 
                $banco->save();
                /* SALVANDOS OS ITENS DO CARRINHO NA TABELA DE PRODUTOS VENDIDOS POR COMANDA */
                $produtos_comanda = new ProdutosComanda();
                for($i = 0; $i < count($carrinho); $i++){
                    $produtos_comanda->create($carrinho[$i]);
                }

                /* APAGANDO TODOS OS ITENS QUE TEM NO CARRINHO COM ID DA COMANDA */
                CarrinhoComanda::where("comanda_id", $comanda->id)->delete();
            });            

            $success = "Comanda fechada com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function imprimir(string|int $id){
        $id = intval($id);
        $comanda = Comanda::find($id);
        $produtos = CarrinhoComanda::where("comanda_id", $id)->get()->toArray();
        //dd($produtos);
        $comanda["produtos"] = [];
        for($i = 0; $i < count($produtos); $i++){
            $produto_estoque = VariaveisProduto::
            join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
            ->select("produtos.nome", "variaveis_produtos.variavel_nome", "variaveis_produtos.preco", "variaveis_produtos.preco_promocao", "variaveis_produtos.promocao")
            ->where("variaveis_produtos.id", $produtos[$i]["variavel_produto_id"])
            ->first();
            $comanda["produtos"] += [$i => array("nome" => $produto_estoque->nome, "variavel_nome" => $produto_estoque->variavel_nome, "preco" => $produto_estoque->promocao == "n" ? $produto_estoque->preco : $produto_estoque->preco_promocao)];
        }

        return view("site/admin/comandas/imprimir", compact("comanda"));
    }

    public function new_request(){
        $pedidos = Pedido::where("status", "n")->get();
        $qtd = count($pedidos);

        return response()->json($qtd);
    }
}
