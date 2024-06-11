<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateEstoque;
use App\Models\CarrinhoComanda;
use App\Models\CarrinhoVenda;
use App\Models\ComandaProduto;
use App\Models\ItemVenda;
use App\Models\Lote;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstoqueController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Produto $produtos, Request $request){

        $search = $request->get("searchProduto");

        if ($search != null){

            $lista_produtos = Produto::where( function ($query) use ($search){
                $query->where("codigo_barras","like", $search)->orWhere("nome","like", "%$search%");
            })->get();

            $qtd_produtos = count($lista_produtos);

            return view("site/admin/estoque/produtos/index", compact('lista_produtos', 'qtd_produtos'));

        } else {

            $qtd_produtos = count(DB::table('produtos')->get('id'));

            $lista_produtos = DB::table('produtos')->get();

            return view("site/admin/estoque/produtos/index", compact('lista_produtos', 'qtd_produtos'));
        }


    }

    public function create() {
        return view('site/admin/estoque/produtos/create');
    }

    public function store(Request $request, Produto $produtos) {
        $verifica_produto = Produto::where('codigo_barras', $request['codigo_barras'])->get();
        if(count($verifica_produto) > 0) {

            $error = "Já existe um produto com esse código de barras";
            session()->flash("error", $error);
            return redirect()->back();
        } else {
            $dados = $request->all();


            $dados['preco'] = str_replace(',', '.', $dados['preco']);
            $dados['preco_custo'] = str_replace(',', '.', $dados['preco_custo']);
            $dados['preco_promocao'] = str_replace(',', '.', $dados['preco_promocao']);
            $dados['lucro'] = $dados['preco'] - $dados['preco_custo'];
            $dados['desconto'] = $dados['preco'] - $dados['preco_promocao'];

            $produtos->create($dados);

            return redirect()->route("admin.estoque.produtos.index");
        }
    }

    public function edit(string|int $id) {
        if (!$produto = Produto::find($id)) {
            return redirect()->back();
        }

        $produto->ult_compra =  date('d/m/Y', strtotime($produto->ult_compra));

        return view("site/admin/estoque/produtos/edit", compact("produto"));

    }

    public function update(StoreUpdateEstoque $request, Produto $produto) {
        if(count(CarrinhoVenda::where("produtos_id", $request->id)->get()) > 0){

            $error = "Erro ao atualizar, pois existe carrinhos PDV ou Comandas com esse item";
            session()->flash("error", $error);

            return redirect()->back();
        }
        $request['preco'] = str_replace(',', '.', $request['preco']);
        $request['preco_custo'] = str_replace(',', '.', $request['preco_custo']);
        $request['preco_promocao'] = str_replace(',', '.', $request['preco_promocao']);

        if (!$produto = $produto->find($request->id)) {
            return redirect()->back();
        }

        $produto->update($request->all());

        $success = "Produto atualizado com sucesso";
        session()->flash("success", $success);
        return redirect()->route('admin.estoque.produtos.index');

    }

    public function destroy(string $id, Produto $produto) {
        $verifica_pdv = CarrinhoVenda::where('produtos_id', $id)->get();
        $verifica_comanda = CarrinhoComanda::where('produtos_id', $id)->get();
        $verifica_lotes = Lote::where('produtos_id', $id)->get();

        if(count($verifica_pdv) > 0 || count($verifica_comanda) > 0 || count($verifica_lotes) > 0) {
            $error = "Erro ao excluir, pois existem vendas ou lotes deste produto";
            session()->flash("error", $error);
            return redirect()->back();
        }

        if (!$produto = $produto->find($id)) {
            $error = "Produto não existe";
            session()->flash("error", $error);
            return redirect()->back();
        } else {

            $produto->delete();

            $success = "Produto excluído com sucesso";
            session()->flash("success", $success);
            return redirect()->route('admin.estoque.produtos.index');
        }

    }

    public function atualizar_promocao(Request $request, Produto $produto) {
        $id = $request->input('id_produto');
        $status = $request->input('status_promocao');
        $produto = Produto::where('id', $id)->first();

        if ($status === "desativado") {

            $produto["promocao"] = "s";
            $produto["lucro"] = $produto["preco_promocao"] - $produto["preco_custo"];
            $produto->save();

            $success = "Promoção ativada";
            session()->flash("success", $success);

            return redirect()->back();
        } else {

            $produto["promocao"] = "n";
            $produto["lucro"] = $produto["preco"] - $produto["preco_custo"];
            $produto->save();

            $success = "Promoção desativada";
            session()->flash("success", $success);

            return redirect()->back();
        }


    }

    public function atualizar_ativo(Request $request) {
        $id = $request->input('id_produto');
        $status = $request->input('status_ativo');

        if ($status === "desativado") {
            Produto::where('id', $id)->update(['ativo' => "s"]);
            $success = "Produto ativado";
            session()->flash("success", $success);
            return redirect()->back();
        } else {
            Produto::where('id', $id)->update(['ativo' => "n"]);
            $success = "Produto desativado";
            session()->flash("success", $success);
            return redirect()->back();
        }


    }

    public function index_lote(){
        $lotes = Lote::join("produtos","lotes.produtos_id","=","produtos.id")->select("produtos.nome", "lotes.quantidade", "lotes.codigo_barras", "lotes.preco", "lotes.preco_custo", "lotes.preco_promocao", "lotes.data_cadastro")->get();
        return view("site/admin/estoque/lotes/index", compact("lotes"));
    }

    public function novo_lote(Request $request){
        if($request->input("quantidade") == null){
            $produto = Produto::where("codigo_barras", $request->input("codigo_barras"))->get();
            if (count($produto) > 0){
                $codigo_barras = $request->input("codigo_barras");
                $divs = '<div class="form-group col-sm-1" style="padding: 3px;">
                            <label for="quantidade" style="margin: 0px;">QTD<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="quantidade" name="quantidade" required autofocus>
                        </div>
                        <div class="form-group col-sm-1" style="padding: 3px;">
                            <label for="preco" style="margin: 0px;">Preço<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco" name="preco" placeholder="R$" required>
                        </div>
                        <div class="form-group col-sm-1" style="padding: 3px;">
                            <label for="preco_custo" style="margin: 0px;">Custo<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco_custo" name="preco_custo" placeholder="R$" required>
                        </div>
                        <div class="form-group col-sm-1" style="padding: 3px;">
                            <label for="preco_promocao" style="margin: 0px;">Promoção<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco_promocao" name="preco_promocao" placeholder="R$" required>
                        </div>';
                session()->flash("codigo_barras", $codigo_barras);
                session()->flash("divs", $divs);
                session()->flash("produtos_id", $produto[0]->id);
                return redirect()->back();
            } else {
                $error = "Produto não cadastrado, cadastre antes de criar lotes";
                session()->flash("error", $error);
                return redirect()->back();
            }
        } else {
            $dados = $request->except("_token");
            $dados['preco'] = str_replace(',', '.', $dados['preco']);
            $dados['preco_custo'] = str_replace(',', '.', $dados['preco_custo']);
            $dados['preco_promocao'] = str_replace(',', '.', $dados['preco_promocao']);
            Lote::create($dados);
            $success = "Lote cadastrado com sucesso";
            session()->flash("success", $success);
            return redirect()->back();
        }        
    }


}
