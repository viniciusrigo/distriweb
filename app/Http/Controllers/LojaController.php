<?php

namespace App\Http\Controllers;

use App\Models\CarrinhoCliente;
use App\Models\CombosProduto;
use App\Models\FormaPagamento;
use App\Models\GlobalConfig;
use App\Models\LocalVenda;
use App\Models\Lote;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\ProdutosPedido;
use App\Models\VariaveisProduto;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LojaController extends Controller
{

    public function index(Request $request){
        if(!auth()->user()){
            return redirect()->route("logincliente");
        }

        $produtos_desconto = VariaveisProduto::where('promocao', 's')->where('pontos', 0)
        ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.*", "variaveis_produtos.*")
        ->orderBy('nome', 'asc')->get()->toArray();
        $produtos_fidelidade = VariaveisProduto::where('promocao', "!=", 's')->where('pontos', ">", 0)
        ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.*")
        ->orderBy('nome', 'asc')->get()->toArray();
        $combos = VariaveisProduto::where('validade', null)
        ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.*")
        ->orderBy('nome', 'asc')->get()->toArray();
        $pedido_aberto = Pedido::where("user_id", auth()->user()->id)->where("status", "!=", "e")->join("zonas", "pedidos.frete", "=", "zonas.entrega")
        ->select("pedidos.*", "zonas.tempo_entrega")->get();

        for($i = 0; $i < count($pedido_aberto); $i++){
            $data_pedido = strtotime($pedido_aberto[$i]->data);
            $entrega_zona = strtotime("1970-01-01 ".$pedido_aberto[$i]->tempo_entrega."UTC");
            $total = $data_pedido + $entrega_zona;

            $pedido_aberto[$i]->tempo_entrega = date("H:i", $total);
        }
        return view("site/cliente/loja/index", compact("produtos_desconto", "produtos_fidelidade", "pedido_aberto", "combos"));
    }

    public function produtos_ajax(Request $request){
        $produtos = VariaveisProduto::
        join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select(
            "produtos.nome",
            "variaveis_produtos.id",
            "variaveis_produtos.produto_id",
            "variaveis_produtos.variavel_nome",
            "variaveis_produtos.codigo_barras",
            "variaveis_produtos.preco",
            "variaveis_produtos.preco_promocao",
            "variaveis_produtos.promocao",
            "variaveis_produtos.pontos",
        )
        ->where('pontos', 0)
        ->where('promocao', "n")
        ->where('variavel_ativo', "s")
        ->where('nome', 'LIKE', "%{$request->busca}%")->orderBy('nome', 'asc')->take(200)->get();

        return $produtos;
    }

    public function add_carrinho(Request $request){
        $dados = $request->except('_token');
        
        $variavel = VariaveisProduto::find($dados['variavel_produto_id']);
        if($variavel->fardo_quantidade == null || $variavel->fardo_quantidade == 0){
            if($variavel->validade != null){
                if($variavel->variavel_quantidade < $dados['quantidade']) {        
                    return false;
                }
            }
        }

        for($x = 0; $x < $dados["quantidade"]; $x++){
            $carrinho = new CarrinhoCliente();
            $carrinho["user_id"] = $dados["usuario"];
            $carrinho["produto_id"] = $variavel->produto_id;
            $carrinho["variavel_produto_id"] = $dados["variavel_produto_id"];
            $carrinho["data"] = now();
            $carrinho->save();
        }

        if(isset($dados["carrinho"])){
            return true;
        } else {
            return redirect()->back();
        }
    }

    public function verifica_carrinho(Request $request){
        $usuario = $request->except('_token');

        $quantidade_produtos = count(CarrinhoCliente::where('user_id', $usuario)->get());

        return $quantidade_produtos;
    }

    public function remover_carrinho(Request $request){
        $produto = CarrinhoCliente::where('variavel_produto_id', $request->variavel_produto_id)->first();

        $produto->delete();

        return redirect()->back();
    }

    public function carrinho(){
        if(!auth()->user()){
            return redirect()->route("logincliente");
        }
        $produtos = CarrinhoCliente::where('user_id', auth()->user()->id)
        ->join("produtos","carrinho_clientes.produto_id","=","produtos.id")
        ->join("variaveis_produtos","carrinho_clientes.variavel_produto_id","=","variaveis_produtos.id")
        ->select(
            'produtos.nome',
            'variaveis_produtos.id',
            'variaveis_produtos.variavel_nome',
            'variaveis_produtos.preco',
            'variaveis_produtos.pontos',
            'variaveis_produtos.lucro',
            'variaveis_produtos.preco_promocao',
            'variaveis_produtos.promocao',
            'variaveis_produtos.codigo_barras'
        )->get();

        $pontos_cliente = auth()->user()->pontos;
        $pontos = 0;
        $lucro = 0;
        for($i = 0; $i < count($produtos); $i++){
            if($produtos[$i]['promocao'] == "s"){
                $produtos[$i]['total_produto'] = $produtos[$i]->preco_promocao;
                $lucro += $produtos[$i]->lucro;
            } else {
                if($produtos[$i]['pontos'] == 0){
                    $produtos[$i]['total_produto'] = $produtos[$i]->preco;
                    $lucro += $produtos[$i]->lucro;
                } else {
                    if($produtos[$i]['pontos'] <= $pontos_cliente){
                        $produtos[$i]['total_produto'] = $produtos[$i]['preco'] - (($produtos[$i]['preco'] * 15) / 100);
                        $pontos_cliente -= $produtos[$i]['pontos'];
                        $lucro += $produtos[$i]->lucro - (($produtos[$i]['preco'] * 15) / 100);
                        $pontos += $produtos[$i]['pontos'];
                        //dd($pontos_cliente);
                    } else {
                        $produtos[$i]['total_produto'] = $produtos[$i]['preco'];
                        $lucro += $produtos[$i]->lucro;
                    }
                }
            }
        }
        //dd($pontos);
        $total = "";
        for($i = 0; $i < count($produtos); $i++){
            global $total;
            $total += $produtos[$i]['total_produto'];
        }
        $zona = Zona::where("id", auth()->user()->zona_id)->first();
        $zonas = Zona::all();
        $produtos_promocao = VariaveisProduto::join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select(
            "produtos.nome",
            "variaveis_produtos.id",
            "variaveis_produtos.variavel_nome",
            "variaveis_produtos.preco",
            "variaveis_produtos.preco_promocao",
        )->where('variaveis_produtos.promocao', 's')
        ->where('variaveis_produtos.pontos', 0)
        ->orderBy("nome", "asc")->get();

        return view('site/cliente/loja/carrinho', compact('produtos', 'total', 'zonas', 'zona', 'pontos', 'lucro', 'produtos_promocao'));
    }

    public function concluir_pedido(Request $request){
        $dados = $request->except('_token');
        $dados["frete"] = str_replace(',', '.', $dados["frete"]);
        //dd($dados);
        $local = LocalVenda::where("id", 2)->first();
        $produtos_carrinho = CarrinhoCliente::where('user_id', $dados["user_id"])
        ->join('variaveis_produtos', 'carrinho_clientes.variavel_produto_id', '=', 'variaveis_produtos.id')
        ->select(
            'variaveis_produtos.id',
            'variaveis_produtos.produto_id',
            'variaveis_produtos.preco',
            'variaveis_produtos.preco_promocao',
            'variaveis_produtos.promocao',
            'variaveis_produtos.pontos',
            'variaveis_produtos.lucro',
            'carrinho_clientes.data'
        )->get();
            
        if($dados['forma_pagamento_id'] > 3){
            if($dados['forma_pagamento_id'] == 'credito'){
                $taxa = FormaPagamento::where('id', $local->credito_id)->first('taxa')->toArray();
                $dados['forma_pagamento_id'] = $local->credito_id;
            } else {
                $taxa = FormaPagamento::where('id', $local->debito_id)->first('taxa')->toArray();
                $dados['forma_pagamento_id'] = $local->debito_id;
            }
        } else {
            $taxa = FormaPagamento::where('id', $dados['forma_pagamento_id'])->first('taxa')->toArray();
        }        

        for($i = 0; $i < count($produtos_carrinho); $i++){

            $variavel = VariaveisProduto::where('id', $produtos_carrinho[$i]->id)->first();

            if($variavel->validade != null){
                if($variavel->fardo_quantidade == null || $variavel->fardo_quantidade == 0){
                    if($variavel->variavel_quantidade < $produtos_carrinho[$i]->quantidade){
                        $error = "Temos somente ".$variavel->quantidade."x";
                        session()->flash("error", $error);
        
                        return redirect()->back();
                    } else {
                        if ($variavel->variavel_quantidade == 0) {
                            $lote = Lote::where("codigo_barras", $variavel->codigo_barras)->orderBy("data_cadastro", "asc")->first();
                            if(isset($lote)){
                                $variavel->variavel_quantidade = $lote->quantidade;
                                $variavel->preco = $lote->preco;
                                $variavel->preco_custo = $lote->preco_custo;
                                $variavel->preco_promocao = $lote->preco_promocao;
                                $variavel->validade = $lote->validade;
                                $variavel->data_cadastro = $lote->data_cadastro;
                
                                $lote->delete();
                            } else {
                                $error = "Temos somente ".$variavel->quantidade."x.";
                                session()->flash("error", $error);
                                
                                return redirect()->back();
                            }
                        }
                        $variavel->variavel_quantidade -= 1;
                        $variavel->ult_compra = $produtos_carrinho[$i]->data;
                        $variavel->save();
                    }
                } else {
                    $produto = Produto::find($variavel->produto_id);
                    $fardo_variavel = VariaveisProduto::find($produto->variavel_produto_id);
                    $fardo_variavel->variavel_quantidade -= $variavel->fardo_quantidade;
                    $fardo_variavel->save();
                }
            } else {
                $produto = Produto::find($variavel->produto_id);
                $combo_produtos = CombosProduto::where("produto_id", $produto->id)->get(["variavel_produto_id", "combo_quantidade"]);
                for($x = 0; $x < count($combo_produtos); $x++){
                    $variavel = VariaveisProduto::where("id", $combo_produtos[$x]->variavel_produto_id)->first();
                    $variavel->variavel_quantidade -= $combo_produtos[$x]->combo_quantidade;
                    $variavel->save();
                }
            }
            
        }
        
        $dados["lucro"] += $dados['frete'];
        
        if ($dados["dinheiro"] != null){
            $dados["troco"] = $dados["dinheiro"] - $dados["total"];
        }

        if (!isset($dados["novo_endereco"])){
            $dados["novo_endereco"] = auth()->user()->logradouro." - ".auth()->user()->bairro;
        }

        $dados["data"] = now();
        $dados["codigo"] = rand(1000, 9999);

        DB::transaction(function() use ($dados,$produtos_carrinho){

            /* CRIANDO O PEDIDO */
            $pedido = Pedido::create($dados);
            
            /* PASSO OS ITENS DO CARRINHO PARA TABELA PRODUTOSPEDIDOS */
            for($i = 0; $i < count($produtos_carrinho); $i++){
                $produtos_pedido = new ProdutosPedido();
                $produtos_pedido['user_id'] = $dados["user_id"];
                $produtos_pedido['pedido_id'] = $pedido->id;
                $produtos_pedido['produto_id'] = $produtos_carrinho[$i]->produto_id;
                $produtos_pedido['variavel_produto_id'] = $produtos_carrinho[$i]->id;
                $produtos_pedido['data'] = $produtos_carrinho[$i]->data;
                $produtos_pedido->save();
            }
            
            /* APAGO TODOS OS ITENS DO CARRINHO */
            CarrinhoCliente::where("user_id", $dados["user_id"])->delete();

        });

        return redirect()->route("loja.index");
    }

    public function cancelar_pedido(Request $request){
        $dado = $request->except('_token');

        $pedido = Pedido::where("id", $dado["pedido_id"])->first();
        if($pedido->status != "n"){
            $error = "Pedido não pode ser cancelado pois a loja já recebeu";
            session()->flash("error", $error);
            return redirect()->back();
        } else {
            $produtos = ProdutosPedido::where("pedido_id", $dado["pedido_id"])->get();
            for($i = 0; $i < count($produtos); $i++){            
                $produtos[$i]->delete();
            }
            $pedido->delete();

            $success = "Pedido cancelado com sucesso";
            session()->flash("success", $success);
            return redirect()->back();
        }
    }

    public function index_confirmar_entrega(){
        return view("site/admin/pedidos/confirmar-entrega");
    }

    public function confirmar_entrega(Request $request){
        $dados_request = $request->all();
        $pedido = Pedido::where("id", $dados_request["pedido_id"])->first();
        $empresa = GlobalConfig::where("id", 1)->first(["codigo_interno"])->toArray();
        if($empresa["codigo_interno"] == $dados_request["codigo_interno"]){
            if($pedido == null){
                $error = "Pedido não encontrado, verifique se o mesmo existe e tente novamente!";
                session()->flash("error", $error);
                return redirect()->route("index-confirmar-entrega");
            } else{
                if($pedido->status != "e"){
                    if($pedido->codigo != $dados_request["codigo"]){
                        $error = "Pedido encontrado, mas o código deo pedido é inválido, tente novamente!";
                        session()->flash("error", $error);
                        return redirect()->route("index-confirmar-entrega");
                    } else {
                        $pedido->status = "e";
                        $pedido->save();
                        $success = "Pedido entregue com sucesso";
                        session()->flash("success", $success);
                        return redirect()->route("index-confirmar-entrega");
                    }
                } else {
                    $error = "Este pedido já foi entregue";
                    session()->flash("error", $error);
                    return redirect()->route("index-confirmar-entrega");
                }
            }
        } else {
            $error = "Código interno incorreto";
            session()->flash("error", $error);
            return redirect()->route("index-confirmar-entrega");
        }
    }

}
