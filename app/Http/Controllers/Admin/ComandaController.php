<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CarrinhoComanda;
use App\Models\Comanda;
use App\Models\ComandaProduto;
use App\Models\Estoque;
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

         $comandas_fechadas = DB::table("comandas")->where("status", "f")->get();

        for($i= 0;$i<count($comandas);$i++) {
            $comandaProduto = DB::table('carrinho_comandas')->where("comandas_id", "=", $comandas[$i]->id)
                                                           ->join("produtos", "carrinho_comandas.produtos_id", "=","produtos.id")
                                                           ->select("carrinho_comandas.id", "produtos.nome", "produtos.preco", "carrinho_comandas.data_compra")
                                                           ->get();

            $comandas[$i]->produtos = $comandaProduto;
            $comandas[$i]->data_abertura = date('H:i:s', strtotime($comandas[$i]->data_abertura));
            $comandas[$i]->data_fechamento = date('H:i:s', strtotime($comandas[$i]->data_fechamento));
        }

        return view("site/admin/comandas/index", compact("comandas", "comandas_fechadas"));
    }

    public function store(Request $request, Comanda $comanda){

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
        $produto_request = $request->all();

        $produto = $produto->where("codigo_barras", "=", $produto_request["codigo_barras"])->get();
        Produto::where("codigo_barras", $produto_request["codigo_barras"])->update(["quantidade" => $produto[0]->quantidade - 1]);

        $comandaProduto = new CarrinhoComanda;
        $comandaProduto->comandas_id = $produto_request["id"];
        $comandaProduto->produtos_id = $produto[0]->id;
        $comandaProduto->data_compra = now()->format('Y-m-d H:i:s');
        $comandaProduto->save();

        $comanda = Comanda::find($produto_request['id']);
        $comanda['total'] = ($comanda['total'] + $produto[0]->preco);
        $comanda['lucro'] += $produto[0]->lucro;
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

        /* REMOVENDO PRODUTO DA COMANDA */
        $produto_carrinho->delete();

        /* ATUALIZANDO VALOR DA COMANDA */
        $comanda["total"] -= $produto_estoque["preco"];
        $comanda["lucro"] -= $produto_estoque["lucro"];
        $comanda->save();

        /* ATUALIZANDO A QUANTIDADE EM ESTOQUE */
        $produto_estoque["quantidade"] += 1;
        $produto_estoque->save();

        $success = "Produto removido com sucesso";
        session()->flash("success", $success);

        return redirect()->back();
    }

    public function closed(Request $request, Comanda $comanda) {
        try {

            $comanda = Comanda::find($request->id);
            $comanda["status"] = "f";
            $comanda["data_fechamento"] = now();
            $comanda["forma_pagamentos_id"] = $request->pagamento;
            $comanda["dinheiro"] = $request->dinheiro;
            $comanda["troco"] = $request->troco;
            $comanda->save();

            $nova_mov = new MovimentacoesFinanceira;
            $nova_mov['ponto_partida'] = "Comanda";
            $nova_mov['cliente_fornecedor'] = $comanda['nome'];
            $nova_mov['valor'] = $comanda['total'];
            $nova_mov['lucro'] = $comanda['lucro'];
            $nova_mov['forma_pagamentos_id'] = $comanda['forma_pagamentos_id'];
            $nova_mov['tipo'] = "e";
            $nova_mov['data'] = $comanda['data_fechamento'];
            $nova_mov->save();

            $success = "Comanda fechada com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
