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
use App\Models\ComandaProduto;
use App\Models\Estoque;
use App\Models\FluxoCaixa;
use App\Models\FormaPagamento;
use App\Models\LocalVenda;
use App\Models\Lote;
use App\Models\MovimentacoesFinanceira;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Redirect;

class ComandaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Comanda $comanda){
        $comandas = DB::table("comandas")
            ->where("status", "a")
            ->get();

         $comandas_fechadas = DB::table("comandas")->where("status", "f")
            ->join("forma_pagamentos", "comandas.forma_pagamentos_id", "=","forma_pagamentos.id")
            ->select(
                "comandas.id",
                "comandas.nome",
                "comandas.total",
                "comandas.lucro",
                "comandas.forma_pagamentos_id",
                "forma_pagamentos.nome as pagamento_nome",
                "comandas.dinheiro",
                "comandas.troco",
                "comandas.status",
                "comandas.data_abertura",
                "comandas.data_fechamento"
            )->get();

        for($i= 0;$i<count($comandas);$i++) {
            $comandaProduto = DB::table('carrinho_comandas')->where("comandas_id", "=", $comandas[$i]->id)
                                                           ->join("produtos", "carrinho_comandas.produtos_id", "=","produtos.id")
                                                           ->select("carrinho_comandas.id", "produtos.nome", "produtos.preco", "carrinho_comandas.quantidade", "carrinho_comandas.data_compra")
                                                           ->get();

            $comandas[$i]->produtos = $comandaProduto;
            $comandas[$i]->data_abertura = date('H:i:s', strtotime($comandas[$i]->data_abertura));
            $comandas[$i]->data_fechamento = date('H:i:s', strtotime($comandas[$i]->data_fechamento));
        }

        return view("site/admin/comandas/index", compact("comandas", "comandas_fechadas"));
    }

    public function store(Request $request, Comanda $comanda){
        $caixa_aberto = Caixa::where('status', 'a')->first();
        if (!isset($caixa_aberto)){

            $alert = "Abra o caixa antes de começar as vendas";
            session()->flash("alert", $alert);

            return redirect()->route("admin.caixa.index");
        }

        $dados = $request->all();
        $dados['data_abertura'] = now()->format('Y-m-d H:i:s');
        $comanda->create($dados);

        $success = "Comanda aberta com sucesso";
        session()->flash("success", $success);

        return redirect()->back();
    }

    public function destroy(string $id) {

        $carrinho_comanda = CarrinhoComanda::where('comandas_id', "=", $id)->get();
        $comanda = Comanda::find($id);

        if(count($carrinho_comanda) >  0){
            $error = "Erro ao excluir, pois existe itens na comanda";
            session()->flash("error", $error);
            return redirect()->back();
        }

        $comanda->delete();

        $success = "Comanda excluída com sucesso";
        session()->flash("success", $success);

        return redirect()->route('admin.comandas.index');
    }

    public function add_produto(Request $request, Produto $produto) {
        $produto_request = $request->except('_token');
        
        /* PEGANDO O PRODUTO DO ESTOQUE */
        $produto_estoque = $produto->where("codigo_barras", "=", $produto_request["codigo_barras"])->first();

        if ($produto_estoque->quantidade == 0) {
            $lote = Lote::where("codigo_barras", $produto_request["codigo_barras"])->orderBy("data_cadastro", "asc")->first();
            if(isset($lote)){
                $produto_estoque->quantidade = $lote->quantidade;
                $produto_estoque->preco = $lote->preco;
                $produto_estoque->preco_custo = $lote->preco_custo;
                $produto_estoque->preco_promocao = $lote->preco_promocao;
                $produto_estoque->validade = $lote->validade;
                $produto_estoque->data_cadastro = $lote->data_cadastro;

                $lote->delete();
            } else {
                $error = "Produto indisponível, verifique o(s) Estoque/Lotes";
                session()->flash("error", $error);
    
                return redirect()->back();
            }
        }
        $produto_estoque->quantidade -= 1;
        $produto_estoque->ult_compra = now(); 
        $produto_estoque->save();
        
        $produto_comanda = CarrinhoComanda::where("produtos_id", $produto_estoque->id)->where("comandas_id", $produto_request["id"])->first();
        if(isset($produto_comanda)){
            $produto_comanda->quantidade += 1;
            $produto_comanda->save();
        } else {
            
            /* ADICONANDO PRODUTO AO CARRINHO DA COMANDA */
            $carrinho = new CarrinhoComanda;
            $carrinho->comandas_id = $produto_request["id"];
            $carrinho->produtos_id = $produto_estoque->id;
            $carrinho->quantidade = 1;
            $carrinho->data_compra = now();
            $carrinho->save();
        }


        /* ATUALIZANDO VALOR TOTAL E LUCRO */
        $comanda = Comanda::find($produto_request['id']);
        $comanda['total'] = $comanda['total'] + ($produto_estoque->promocao == "n" ? $produto_estoque->preco : $produto_estoque->preco_promocao);
        $comanda['lucro'] += $produto_estoque->lucro;
        $comanda->save();

        $success = "Produto adicionado com sucesso";
        session()->flash("success", $success);

        return redirect()->back();
    }

    public function remove_produto(Request $resquest) {

        $produto_request = $resquest->all();
        $produto_carrinho = CarrinhoComanda::find($produto_request['id']);
        $comanda = Comanda::find($produto_request['comandas_id']);
        $produto_estoque = Produto::find($produto_carrinho["produtos_id"]);

        /* REMOVENDO PRODUTO DO CARRINHO DA COMANDA */
        if($produto_carrinho->quantidade == 1){
            $produto_carrinho->delete();
            $success = "Produto removido com sucesso";
            session()->flash("success", $success);
        } else {
            $produto_carrinho->quantidade -= 1;
            $produto_carrinho->save();
            $success = "Removido 1 unidade deste produto";
            session()->flash("success", $success);
        }

        /* ATUALIZANDO VALOR DA COMANDA */
        $comanda["total"] -= $produto_estoque["preco"];
        $comanda["lucro"] -= $produto_estoque["lucro"];
        $comanda->save();

        /* ATUALIZANDO A QUANTIDADE EM ESTOQUE */
        $produto_estoque["quantidade"] += 1;
        $produto_estoque->save();

        return redirect()->back();
    }

    public function closed(Request $request, Comanda $comanda) {
        // try {
            $comanda = Comanda::find($request->id);
            $local = LocalVenda::find(3);
            $carrinho = CarrinhoComanda::where("comandas_id", $request->id)->get()->toArray();
            if($request["pagamento"] == 'credito'){
                $comanda->forma_pagamentos_id = $local->credito_id;
            } else if ($request["pagamento"] == 'debito') {
                $comanda->forma_pagamentos_id = $local->debito_id;
            } else {
                $comanda->forma_pagamentos_id = $request["pagamento"];
            }
            $nova_mov = new MovimentacoesFinanceira;
            $nova_venda = new Venda();
            $taxa_pagamento = FormaPagamento::where("id", $comanda->forma_pagamentos_id)->first();
            
            /* REALIZAÇÃO DA TAXAÇÃO DO VALOR TOTAL */
            if ($comanda->forma_pagamentos_id > "3") {
                $comanda->total = $comanda->total - ($comanda->total / 100 * $taxa_pagamento->taxa);
                $comanda->lucro -= round(($comanda->total * 100) / (100 - $taxa_pagamento->taxa) - $comanda->total, 2);
                $comanda->taxa = $taxa_pagamento->taxa;
            }
            
            $banco = Banco::where("id", $taxa_pagamento->banco_id)->first();

            $comanda->dinheiro = $request["dinheiro"];
            $comanda->troco = $request["troco"];
            $comanda->status = "f";
            $comanda->data_fechamento = now();
            $comanda->save();
            
            if($request["pagamento"] == "2"){

                /* NOVO FLUXO */
                $caixa_aberto = Caixa::where('status', 'a')->first("id");
                $novo_fluxo = new FluxoCaixa();
                $novo_fluxo["caixas_id"] = $caixa_aberto->id;
                $novo_fluxo["venda"] = $comanda->total;
                $novo_fluxo["dinheiro"] = $request->dinheiro;
                $novo_fluxo["troco"] = $request->troco;
                $novo_fluxo["data"] = now();
                $novo_fluxo->save();
            }

            /* NOVA MOVIMENTAÇÃO */
            $nova_mov['local_id'] = 3;
            $nova_mov['cliente_fornecedor'] = $comanda->nome;
            $nova_mov['valor'] = $comanda->total;
            $nova_mov['lucro'] = $comanda->lucro;
            $nova_mov['forma_pagamentos_id'] = $comanda->forma_pagamentos_id;
            $nova_mov['tipo'] = "e";
            $nova_mov['data'] = $comanda->data_fechamento;
            $nova_mov->save();

            /* NOVA VENDA */
            $nova_venda['local_id'] = 3;
            $nova_venda['valor'] = $comanda->total;
            $nova_venda['lucro'] = $comanda->lucro;
            $nova_venda['comandas_id'] = $comanda->id;
            $nova_venda['forma_pagamentos_id'] = $comanda->forma_pagamentos_id;
            $nova_venda['taxa'] = $taxa_pagamento->taxa;
            $nova_venda['dinheiro'] = $comanda->dinheiro;
            $nova_venda['troco'] = $comanda->troco;
            $nova_venda['status'] = 'f';
            $nova_venda['data_venda'] = $comanda->data_fechamento;
            $nova_venda->save();

            $fluxo_banco = new FluxoBanco();
            $fluxo_banco->local_id = $banco->id;
            $fluxo_banco->valor = $nova_venda['valor'];
            $fluxo_banco->tipo = "e";
            $fluxo_banco->data = now();
            $fluxo_banco->save();  
            
            $banco->saldo += $comanda->total;
            $banco->save();

            /* SALVANDOS OS ITENS DO CARRINHO NA TABELA DE PRODUTOS VENDIDOS POR COMANDA */
            $produtos_comanda = new ProdutosComanda();
            for($i = 0; $i < count($carrinho); $i++){
                $produtos_comanda->create($carrinho[$i]);
            }

            /* APAGANDO TODOS OS ITENS QUE TEM NO CARRINHO COM ID DA COMANDA */
            CarrinhoComanda::where("comandas_id", $comanda->id)->delete();

            $success = "Comanda fechada com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        // } catch (Exception $e) {
        //     return redirect()->back()->with('error', $e->getMessage());
        // }
    }

    public function imprimir(string|int $id){
        $id = intval($id);
        $comanda = Comanda::find($id);
        $produtos = CarrinhoComanda::where("comandas_id", $id)->get()->toArray();
        //dd($produtos);
        $comanda["produtos"] = [];
        for($i = 0; $i < count($produtos); $i++){
            $produto_estoque = Produto::where("id", $produtos[$i]["produtos_id"])->select('nome', 'preco', 'preco_promocao', 'promocao')->first();
            $comanda["produtos"] += [$i => array("nome" => $produto_estoque->nome, "quantidade" => $produtos[$i]["quantidade"], "preco" => $produto_estoque->promocao == "n" ? $produto_estoque->preco : $produto_estoque->preco_promocao)];
        }

        return view("site/admin/comandas/imprimir", compact('comanda'));
    }
}
