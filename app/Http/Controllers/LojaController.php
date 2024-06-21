<?php

namespace App\Http\Controllers;

use App\Models\CarrinhoCliente;
use App\Models\FormaPagamento;
use App\Models\LocalVenda;
use App\Models\Lote;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\ProdutosPedido;
use App\Models\Zona;
use Illuminate\Http\Request;

class LojaController extends Controller
{

    public function index(Request $request){
        if(!auth()->user()){
            return redirect()->route("logincliente");
        }
        $produtos = Produto::where('nome', 'LIKE', "%{$request->busca}%")->orderBy('nome', 'asc')->get();
        $produtos_desconto = Produto::where('promocao', "s")->orderBy('nome', 'asc')->get();

        return view("site/cliente/loja/index", compact("produtos", "produtos_desconto"));
    }

    public function produtos_ajax(Request $request){
        $produtos = Produto::where('nome', 'LIKE', "%{$request->busca}%")->orderBy('nome', 'asc')->get()->toJson();
        return $produtos;
    }

    public function add_carrinho(Request $request){
        $dados = $request->except('_token');

        $produto_estoque = Produto::find($dados['produto']);
        if($produto_estoque->quantidade < $dados['qtd']) {
            $error = '<div style="background: #ff9b9b; border-left: 8px solid #ff0202;" class="alert hide">
                        <span style="color: #ce0000;" class="fas fa-solid fa-xmark"></span>
                        <span style="color: #ce0000;" class="msg">Temos somente '.$produto_estoque->quantidade.' deste produto</span>
                    </div>';

            return $error;
        }

        $produto = CarrinhoCliente::where('users_id', $dados["usuario"])->where('produtos_id', $dados["produto"])->first();
        if($produto){
            $produto->qtd += $dados["qtd"];
            $produto->save();

            $success = '<div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
                        <span style="color: #ffffff;" class="fas fa-solid fa-xmark"></span>
                        <span style="color: #ffffff;" class="msg">Produto adicionado ao carrinho</span>
                    </div>';

            return $success;
        }

        $carrinho = new CarrinhoCliente();
        $carrinho["users_id"] = $dados["usuario"];
        $carrinho["produtos_id"] = $dados["produto"];
        $carrinho["qtd"] = $dados["qtd"];
        $carrinho["data"] = now();
        $carrinho->save();

        $success = '<div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
                        <span style="color: #ffffff;" class="fas fa-solid fa-circle-check"></span>
                        <span style="color: #ffffff;" class="msg">Produto adicionado ao carrinho</span>
                    </div>';

        return $success;
    }

    public function verifica_carrinho(Request $request){
        $usuario = $request->except('_token');

        $qtd_produtos = count(CarrinhoCliente::where('users_id', $usuario)->get());

        return $qtd_produtos;
    }

    public function remover_carrinho(Request $request){
        CarrinhoCliente::where('produtos_id', $request->produto_id)->delete();

        $success = "Produto retirado do carrinho";
        session()->flash("success", $success);

        return redirect()->back();
    }

    public function carrinho(){
        if(!auth()->user()){
            return redirect()->route("logincliente");
        }
        $produtos = CarrinhoCliente::where('users_id', auth()->user()->id)->join('produtos','carrinho_clientes.produtos_id','=','produtos.id')->select(
            'produtos.id', 'produtos.nome', 'produtos.preco', 'produtos.pontos','produtos.preco_promocao', 'produtos.promocao', 'carrinho_clientes.qtd', 'produtos.codigo_barras'
        )->get();
        
        for($i = 0; $i < count($produtos); $i++){
            
            if($produtos[$i]['promocao'] == "s"){
                $produtos[$i]['total_produto'] = $produtos[$i]->qtd * $produtos[$i]->preco_promocao;
            } else {
                $produtos[$i]['total_produto'] = $produtos[$i]->qtd * $produtos[$i]->preco;
            }
        }
        
        $total = "";
        $pontos = "";
        for($i = 0; $i < count($produtos); $i++){
            global $total;
            global $pontos;
            $total += $produtos[$i]['total_produto'];
            if($produtos[$i]['pontos'] > 0){
                $pontos += $produtos[$i]['promocao'] == "n" ? round($produtos[$i]['preco'], 0, PHP_ROUND_HALF_DOWN) : round($produtos[$i]['preco_promocao'], 0, PHP_ROUND_HALF_DOWN);
            }
        }
        $zona = Zona::where("id", auth()->user()->zona_id)->first();
        $zonas = Zona::all();

        return view('site/cliente/loja/carrinho', compact('produtos', 'total', 'pontos', 'zonas', 'zona'));
    }

    public function concluir_pedido(Request $request){
        $dados = $request->except('_token');
        $local = LocalVenda::where("id", 2)->first();
        $carrinho = CarrinhoCliente::where("users_id", $dados["users_id"])->get();
        $dados["lucro"] = 0;
        $total = "";
        $pontos = "";
        $produtos = CarrinhoCliente::where('users_id', $dados["users_id"])->join('produtos', 'carrinho_clientes.produtos_id', '=', 'produtos.id')->select(
            'produtos.id',
            'carrinho_clientes.qtd',
            'produtos.preco',
            'produtos.preco_promocao',
            'produtos.promocao',
            'produtos.pontos',
            'produtos.lucro',
            'carrinho_clientes.data',
            'carrinho_clientes.data'
        )->get();

        if($dados['forma_pagamentos_id'] > 3){
            if($dados['forma_pagamentos_id'] == 'credito'){
                $taxa = FormaPagamento::where('id', $local->credito_id)->first('taxa')->toArray();
                $dados['forma_pagamentos_id'] = $local->credito_id;
            } else {
                $taxa = FormaPagamento::where('id', $local->debito_id)->first('taxa')->toArray();
                $dados['forma_pagamentos_id'] = $local->debito_id;
            }
        } else {
            $taxa = FormaPagamento::where('id', $dados['forma_pagamentos_id'])->first('taxa')->toArray();
        }        

        for($i = 0; $i < count($produtos); $i++){

            $produto_estoque = Produto::where('id', $produtos[$i]->id)->first();

            if($produto_estoque->quantidade < $produtos[$i]->qtd){
                $error = "Temos somente ".$produto_estoque->quantidade."x ".$produto_estoque->nome;
                session()->flash("error", $error);

                return redirect()->back();
            } else {
                $dados["lucro"] += $produto_estoque->lucro * $produtos[$i]->qtd;
                if ($produto_estoque->quantidade == 0) {
                    $lote = Lote::where("codigo_barras", $produto_estoque->codigo_barras)->orderBy("data_cadastro", "asc")->first();
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
                $produto_estoque->quantidade -= $produtos[$i]->qtd;
                $produto_estoque->ult_compra = $produtos[$i]->data;
                $produto_estoque->save();
            }
        }

        if(!isset($dados['zona'])){
            if (auth()->user()->zona == "Norte"){
                $dados['frete'] = 5;
            } else if (auth()->user()->zona == "Sul") {
                $dados['frete'] = 20;
            } else if (auth()->user()->zona == "Oeste") {
                $dados['frete'] = 15;
            } else {
                $dados['frete'] = 15;
            }
        }
        
        for($i = 0; $i < count($produtos); $i++){
            global $total;
            global $pontos;
            if ($produtos[$i]->promocao == "s"){
                $total += $produtos[$i]->preco_promocao * $produtos[$i]->qtd;
                
            } else {
                $total += $produtos[$i]->preco * $produtos[$i]->qtd;
            }
            if($produtos[$i]['pontos'] > 0){
                $pontos += $produtos[$i]['promocao'] == "n" ? round($produtos[$i]['preco'], 0, PHP_ROUND_HALF_DOWN) : round($produtos[$i]['preco_promocao'], 0, PHP_ROUND_HALF_DOWN);
            }
        }

        $dados["pontos"] = $pontos;
        $dados["total"] = $total + $dados["frete"];
        $dados["lucro"] += $dados['frete'];
        
        if ($dados["dinheiro"] != null){
            $dados["troco"] = $dados["dinheiro"] - $dados["total"];
        }

        if (!isset($dados["novo_endereco"])){
            $dados["novo_endereco"] = auth()->user()->logradouro." - ".auth()->user()->bairro;
        }

        $dados["data"] = now();

        /* CRIANDO O PEDIDO */
        $pedido = Pedido::create($dados);

        /* PASSO OS ITENS DO CARRINHO PARA TABELA PRODUTOSPEDIDOS */
        for($i = 0; $i < count($carrinho); $i++){
            $produtos_pedido = new ProdutosPedido();
            $produtos_pedido['users_id'] = $dados["users_id"];
            $produtos_pedido['pedidos_id'] = $pedido->id;
            $produtos_pedido['produtos_id'] = $carrinho[$i]->produtos_id;
            $produtos_pedido['qtd'] = $carrinho[$i]->qtd;
            $produtos_pedido['data'] = $carrinho[$i]->data;
            $produtos_pedido->save();
        }

        /* APAGO TODOS OS ITENS DO CARRINHO */
        CarrinhoCliente::where("users_id", $dados["users_id"])->delete();

        return redirect()->back();
    }
}
