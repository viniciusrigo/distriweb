<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateEstoque;
use App\Models\CarrinhoCliente;
use App\Models\CarrinhoComanda;
use App\Models\CarrinhoVenda;
use App\Models\CarrinhoVendaManual;
use App\Models\CategoriaProduto;
use App\Models\CombosProduto;
use App\Models\Lote;
use App\Models\Manifesto;
use App\Models\Produto;
use App\Models\ProdutosComanda;
use App\Models\ProdutosPedido;
use App\Models\ProdutosVenda;
use App\Models\VariaveisProduto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstoqueController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth");
    }

    public function page_index(Produto $produtos, Request $request){
        $lista_produtos = Produto::join("categoria_produtos", "produtos.categoria_id", "=", "categoria_produtos.id")
        ->select("produtos.*", "categoria_produtos.nome as categoria_nome")
        ->get();
        $quantidade_produtos = count($lista_produtos);

        return view("site/admin/estoque/produtos/index", compact("lista_produtos", "quantidade_produtos"));
    }

    public function page_create() {
        $categorias = CategoriaProduto::all();
        $produtos = VariaveisProduto::where("categoria_id", "!=", 5)->where("categoria_id", "!=", 6)
        ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.id","variaveis_produtos.variavel_nome")
        ->orderBy("nome", "asc")->get();

        return view("site/admin/estoque/produtos/create", compact("categorias", "produtos"));
    }

    public function page_create_combo() {
        $categorias = CategoriaProduto::all();
        $produtos = VariaveisProduto::where("categoria_id", "!=", 5)->where("categoria_id", "!=", 6)
        ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.id","variaveis_produtos.variavel_nome")
        ->orderBy("nome", "asc")->get();

        return view("site/admin/estoque/produtos/create-combo", compact("categorias", "produtos"));
    }

    public function store(Request $request, Produto $produto, VariaveisProduto $variaveisProduto) {
        try{
            /* VERIFICA SE O PRODUTO É UM COMBO(5), CODIGO_BARRAS == NULL = COMBO */
            if($request["codigo_barras"] != null){
                /* VERIFICA SE A VARIAVEL TEM PRODUTO, PRODUTO_ID == NULL = NOVO PRODUTO/VARIAVEL */
                if($request->input("produto_id") == null){
                    $dados_request["nome"] = $request->input("nome");
                    $dados_request["categoria_id"] = $request->input("categoria_id");
                    /* VERIFICA SE É FARDO */
                    if($dados_request["categoria_id"] == 6){
                        $dados_request["variavel_produto_id"] = $request->input("variavel_produto_id");                    
                    }
                    $dados_request["sku"] = $request->input("sku");
                    $dados_request["ativo"] = "s";

                    $produto_criado = $produto->create($dados_request);
                }
                /* CRIANDO AS VARIAVEIS DO PRODUTO */
                for($x = 0; $x < count($request["variavel_nome"]); $x++){
                    $verifica_produto = VariaveisProduto::where("codigo_barras", $request["codigo_barras"][$x])->get();
                    if(count($verifica_produto) > 0) {
                        $error = "Já existe um produto com esse código de barras";
                        session()->flash("error", $error);

                        return redirect()->back();
                    } else {
                        if($request->input("produto_id") == null){
                            $variavel_dados["produto_id"] = $produto_criado->id;
                        } else {
                            $variavel_dados["produto_id"] = $request->input("produto_id");
                        }
                        $variavel_dados["variavel_nome"] = $request["variavel_nome"][$x];
                        $variavel_dados["variavel_quantidade"] = $request["variavel_quantidade"][$x];
                        if($request->input("produto_id") == null){
                            if($produto_criado->categoria_id == 6){
                                $variavel_dados["fardo_quantidade"] = $request["fardo_quantidade"][$x];
                            }
                        }
                        $variavel_dados["pontos"] = $request["pontos"][$x];
                        $variavel_dados["preco"] = str_replace(",", ".", $request["preco"][$x]);
                        $variavel_dados["preco_custo"] = str_replace(",", ".", $request["preco_custo"][$x]);
                        $variavel_dados["preco_promocao"] = str_replace(",", ".", $request["preco_promocao"][$x]);
                        $variavel_dados["lucro"] = ($variavel_dados["preco"] - $variavel_dados["preco_custo"]);
                        $variavel_dados["codigo_barras"] = $request["codigo_barras"][$x];
                        $variavel_dados["validade"] = $request["validade"][$x];
                        $variavel_dados["promocao"] = "n";
                        $variavel_dados["variavel_ativo"] = "s";
                        $variavel_dados["ult_compra"] = null;
                        $variavel_dados["data_cadastro"] = now();
                        try{
                            $variaveisProduto->create($variavel_dados);
                        } catch (Exception $e) {
                            return redirect()->back()->with("error", $e->getMessage());
                        }
                    }
                }

                $success = "Produto cadastrado com sucesso";
                session()->flash("success", $success);

                return redirect()->back();
            } else {
                $dados_request["nome"] = $request->input("nome");
                $dados_request["categoria_id"] = $request->input("categoria_id");
                if($dados_request["categoria_id"] == 6){
                    $dados_request["variavel_produto_id"] = $request->input("variavel_produto_id");
                }
                $dados_request["sku"] = $request->input("sku");
                $dados_request["ativo"] = $request->input("s");

                $produto_criado = $produto->create($dados_request);

                $variavel_dados["produto_id"] = $produto_criado->id;
                $variavel_dados["variavel_nome"] = $request["variavel_nome"][0];
                $variavel_dados["variavel_quantidade"] = $request["variavel_quantidade"][0];
                $variavel_dados["fardo_quantidade"] = $request["fardo_quantidade"][0];
                $variavel_dados["pontos"] = $request["pontos"][0];
                $variavel_dados["preco"] = str_replace(",", ".", $request["preco"][0]);
                $variavel_dados["preco_custo"] = str_replace(",", ".", $request["preco_custo"][0]);
                $variavel_dados["preco_promocao"] = str_replace(",", ".", $request["preco_promocao"][0]);
                $variavel_dados["lucro"] = ($variavel_dados["preco"] - $variavel_dados["preco_custo"]);
                $variavel_dados["codigo_barras"] = $request["codigo_barras"][0];
                $variavel_dados["validade"] = $request["validade"][0];
                $variavel_dados["promocao"] = "n";
                $variavel_dados["variavel_ativo"] = "s";
                $variavel_dados["ult_compra"] = null;
                $variavel_dados["data_cadastro"] = now();

                $variaveisProduto->create($variavel_dados);

                $success = "Produto cadastrado com sucesso";
                session()->flash("success", $success);

                return redirect()->back();
            }
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function store_combo(Request $request, Produto $produto, VariaveisProduto $variaveisProduto) {
        try{
            $produto_dados["nome"] = $request->input("nome");
            $produto_dados["categoria_id"] = $request->input("categoria_id");
            $produto_dados["ativo"] = "s";
            $produto_criado = $produto->create($produto_dados);

            $variavel_dados["produto_id"] = $produto_criado->id;
            $variavel_dados["variavel_nome"] = $request->input("variavel_nome");
            $variavel_dados["preco"] = str_replace(",", ".", $request->input("preco"));
            $variavel_dados["preco_custo"] = str_replace(",", ".", $request->input("preco_custo"));
            $variavel_dados["lucro"] = $variavel_dados["preco"] - $variavel_dados["preco_custo"];
            $variavel_dados["variavel_ativo"] = "s";
            $variavel_dados["data_cadastro"] = now();
            $variaveisProduto->create($variavel_dados);

            $variaveis = $request->input("variavel_produto_id");
            $quantidade = $request->input("combo_quantidade");
            for($x = 0; $x < count($variaveis); $x++){
                $combo_produtos = new CombosProduto();
                $combo_produtos->produto_id = $produto_criado->id;
                $combo_produtos->variavel_produto_id = $variaveis[$x];
                $combo_produtos->combo_quantidade = $quantidade[$x];
                $combo_produtos->save();
            }

            $success = "Combo criado com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }

    }

    public function page_edit(string|int $id) {
        if (!$produto = Produto::find($id)) {
            return redirect()->back();
        }

        $produto->ult_compra =  date("d/m/Y", strtotime($produto->ult_compra));
        $categorias = CategoriaProduto::all();
        $variaveis = VariaveisProduto::where("produto_id", $id)->get();

        if($produto->categoria_id == 5){
            $combo_produtos = CombosProduto::join("variaveis_produtos", "combos_produtos.variavel_produto_id", "=", "variaveis_produtos.id")
            ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
            ->select("produtos.nome", "variaveis_produtos.id", "variaveis_produtos.variavel_nome", "combos_produtos.combo_quantidade")
            ->where("combos_produtos.produto_id", $produto->id)->get()->toArray();

            $produtos = VariaveisProduto::where("categoria_id", "!=", 5)->where("categoria_id", "!=", 6)
            ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
            ->select("produtos.nome", "variaveis_produtos.id","variaveis_produtos.variavel_nome")
            ->orderBy("nome", "asc")->get()->toArray();

            return view("site/admin/estoque/produtos/edit", compact("produto", "categorias", "variaveis", "combo_produtos", "produtos"));
        }
        $lotes = [];
        for($x = 0; $x < count($variaveis); $x++){
            $lote = Lote::where("codigo_barras", $variaveis[$x]->codigo_barras)->first();
            if($lote != []){
                array_push($lotes, $lote);
            }
        }

        if(count($lotes) > 0){
            return view("site/admin/estoque/produtos/edit", compact("produto", "categorias", "variaveis", "lotes"));
        } else {
            return view("site/admin/estoque/produtos/edit", compact("produto", "categorias", "variaveis"));
        }
    }

    public function update(StoreUpdateEstoque $request, Produto $produto) {
        try{
            if(count(CarrinhoVenda::where("produto_id", $request->id)->get()) > 0){
                $error = "Erro ao atualizar, pois existe carrinhos PDV ou Comandas com esse item";
                session()->flash("error", $error);
                
                return redirect()->back();
            }
            
            if (!$produto = $produto->find($request->produto_id)) {
                $error = "Produto não encontrado";
                session()->flash("error", $error);

                return redirect()->back();
            }
            
            $produto->update($request->all());

            $success = "Produto atualizado com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function update_variable(StoreUpdateEstoque $request, VariaveisProduto $variavel_produto) {
        try{
            if(count(CarrinhoVenda::where("variavel_produto_id", $request->input("variavel_produto_id"))->get()) > 0 || count(CarrinhoComanda::where("variavel_produto_id", $request->input("variavel_produto_id"))->get()) > 0 || count(CarrinhoVendaManual::where("variavel_produto_id", $request->input("variavel_produto_id"))->get()) > 0){
                $error = "Erro ao atualizar, pois existe carrinhos PDVs/Comandas/Venda Manual com esse item";
                session()->flash("error", $error);

                return redirect()->back();
            }

            $path_image = "";

            if ($request->hasFile("path_image") && $request->file("path_image")->isValid()){
                global $path_image;
                $variavel = VariaveisProduto::where("id", $request->input("variavel_produto_id"))->first(["id"]);
                $codigo = $variavel->id;
                $extensao = pathinfo($request->file("path_image")->getClientOriginalName(), PATHINFO_EXTENSION);
                $file_name = $codigo.".".$extensao;
                $path_image = $request->file("path_image")->storeAs("variaveis_produtos", $file_name);
            }

            $request["preco"] = str_replace(",", ".", $request["preco"]);
            $request["preco_custo"] = str_replace(",", ".", $request["preco_custo"]);
            if($request["categoria_id"] != 5){
                $request["preco_promocao"] = str_replace(",", ".", $request["preco_promocao"]);
            }
            $request["lucro"] = ($request["preco"] - $request["preco_custo"]);

            $produto = $variavel_produto->find($request->variavel_produto_id);
            $produto->update($request->all());

            $success = "Variavel atualizada com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function destroy(string $id, VariaveisProduto $variavel) {
        try{
            $verifica_carrinho_pdv = CarrinhoVenda::where("variavel_produto_id", $id)->get();
            $verifica_carrinho_comanda = CarrinhoComanda::where("variavel_produto_id", $id)->get();
            $verifica_carrinho_pedidos = CarrinhoCliente::where("variavel_produto_id", $id)->get();
            $verifica_lotes = Lote::where("variavel_produto_id", $id)->get();

            $verifica_vendas_pdv = ProdutosVenda::where("variavel_produto_id", $id)->get();
            $verifica_vendas_comanda = ProdutosComanda::where("variavel_produto_id", $id)->get();
            $verifica_vendas_pedidos = ProdutosPedido::where("variavel_produto_id", $id)->get();

            if(count($verifica_carrinho_pdv) > 0 || count($verifica_carrinho_comanda) > 0 || count($verifica_carrinho_pedidos) > 0 || count($verifica_lotes) > 0) {
                $error = "Erro ao deletar, este produto esta em algum Carrinho de Venda";
                session()->flash("error", $error);

                return redirect()->back();
            }

            if(count($verifica_vendas_pdv) > 0 || count($verifica_vendas_comanda) > 0 || count($verifica_vendas_pedidos) > 0) {
                $error = "Erro ao excluir, pois existem venda(s) feita(s) com este produto";
                session()->flash("error", $error);

                return redirect()->back();
            }

            $variavel = $variavel->where("id", $id)->first();
            $variaveis = VariaveisProduto::where("produto_id", $variavel->produto_id)->get();
            $qtd_variaveis = count($variaveis);

            if($qtd_variaveis == 1){
                $produto = Produto::where("id", $variavel->produto_id)->first();

                DB::transaction(function() use ($produto, $variavel){             
                    if($produto->categoria_id == 5){
                        CombosProduto::where("produto_id", $produto->id)->delete();
                    } else {
                        $variavel->delete();
                        $produto->delete();
                    }
                });

                $success = "Produto e Variavel excluídos com sucesso";
            } else {
                DB::transaction(function() use ($variavel){
                    $variavel->delete();
                });
                $success = "Variavel excluída com sucesso";
            }
            session()->flash("success", $success);

            return redirect()->route("admin.estoque.produtos.page-index");
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function variable_promotion(Request $request) {
        try{
            $variavel_id = $request->input("variavel_id");
            $status = $request->input("status_promocao");
            $variavel = VariaveisProduto::find($variavel_id);

            $carrinho_venda = CarrinhoVenda::where("variavel_produto_id", $variavel_id)->get();
            $carrinho_comanda = CarrinhoComanda::where("variavel_produto_id", $variavel_id)->get();
            $carrinho_venda_manual = CarrinhoVendaManual::where("variavel_produto_id", $variavel_id)->get();

            if(count($carrinho_comanda) > 0 || count($carrinho_venda) > 0 || count($carrinho_venda_manual) > 0){
                $error = "Erro ao desativar Promoção, este produto esta em algum Carrinho de Venda";
                session()->flash("error", $error);

                return redirect()->back();
            }

            if ($status === "desativado") {
                $variavel["promocao"] = "s";
                $variavel["lucro"] = $variavel["preco_promocao"] - $variavel["preco_custo"];
                $variavel->save();

                $success = "Promoção ativada";
                session()->flash("success", $success);

                return redirect()->back();
            } else {
                $variavel["promocao"] = "n";
                $variavel["lucro"] = $variavel["preco"] - $variavel["preco_custo"];
                $variavel->save();

                $success = "Promoção desativada";
                session()->flash("success", $success);

                return redirect()->back();
            }
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function variable_status(Request $request) {
        try{
            $variavel_id = $request->input("variavel_id");
            $status = $request->input("status_ativo");

            $carrinho_venda = CarrinhoVenda::where("variavel_produto_id", $variavel_id)->get();
            $carrinho_comanda = CarrinhoComanda::where("variavel_produto_id", $variavel_id)->get();
            $carrinho_venda_manual = CarrinhoVendaManual::where("variavel_produto_id", $variavel_id)->get();

            if(count($carrinho_comanda) > 0 || count($carrinho_venda) > 0 || count($carrinho_venda_manual) > 0){
                $error = "Erro ao desativar, este produto esta em algum Carrinho de Venda";
                session()->flash("error", $error);

                return redirect()->back();
            }

            if ($status === "desativado") {
                VariaveisProduto::where("id", $variavel_id)->update(["variavel_ativo" => "s"]);

                $success = "Produto ativado";
                session()->flash("success", $success);

                return redirect()->back();
            } else {
                VariaveisProduto::where("id", $variavel_id)->update(["variavel_ativo" => "n"]);

                $success = "Produto desativado";
                session()->flash("success", $success);

                return redirect()->back();
            }
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function page_batch(){
        $lotes = Lote::
        join("produtos","lotes.produto_id","=","produtos.id")
        ->join("variaveis_produtos","lotes.variavel_produto_id","=","variaveis_produtos.id")
        ->select("produtos.nome", "variaveis_produtos.variavel_nome", "lotes.quantidade", "variaveis_produtos.codigo_barras", "lotes.preco", "lotes.preco_custo", "lotes.preco_promocao", "lotes.validade", "lotes.data_cadastro")
        ->get();

        return view("site/admin/estoque/lotes/index", compact("lotes"));
    }

    public function new_batch(Request $request){
        try{
            if($request->input("quantidade") == null){
                $variavel = VariaveisProduto::where("codigo_barras", $request->input("codigo_barras"))->get();

                if (count($variavel) > 0){
                    $codigo_barras = $request->input("codigo_barras");
                    $divs = '<div class="m-1" style="width: 55px">
                                <label for="quantidade" style="margin: 0px;">QTD<code>*</code></label>
                                <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="quantidade" name="quantidade" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13"  maxlength="4" required autofocus>
                            </div>
                            <div class="m-1" style="width: 75px">
                                <label for="preco" style="margin: 0px;">Preço<code>*</code></label>
                                <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco" name="preco" placeholder="R$" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                            </div>
                            <div class="m-1" style="width: 75px">
                                <label for="preco_custo" style="margin: 0px;">Custo<code>*</code></label>
                                <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco_custo" name="preco_custo" placeholder="R$" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                            </div>
                            <div class="m-1" style="width: 95px">
                                <label for="preco_promocao" style="margin: 0px;">Promoção<code>*</code></label>
                                <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco_promocao" name="preco_promocao" placeholder="R$" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                            </div>
                            <div class="m-1">
                                <label for="validade" style="margin: 0px;">Validade<code>*</code></label>
                                <input type="date" style="margin: 0px;" class="form-control form-control-border border-width-2" id="validade" name="validade" required>
                            </div>';

                    session()->flash("codigo_barras", $codigo_barras);
                    session()->flash("divs", $divs);
                    session()->flash("variavel_produto_id", $variavel[0]->id);
                    session()->flash("produto_id", $variavel[0]->produto_id);

                    return redirect()->back();
                } else {
                    $error = "Produto não cadastrado, cadastre antes de criar lotes";
                    session()->flash("error", $error);

                    return redirect()->back();
                }
            } else {
                $dados = $request->except("_token");
                $dados["preco"] = str_replace(",", ".", $dados["preco"]);
                $dados["preco_custo"] = str_replace(",", ".", $dados["preco_custo"]);
                $dados["preco_promocao"] = str_replace(",", ".", $dados["preco_promocao"]);

                Lote::create($dados);

                $variavel = VariaveisProduto::where("codigo_barras", $dados["codigo_barras"])->first(["id"])->toArray();
                $combo_variavel = CombosProduto::where("variavel_produto_id", $variavel["id"])->get();

                if(count($combo_variavel) > 0){
                    $success = "Existe combo com este item";
                    session()->flash("success", $success);

                    return redirect()->back();
                }
                $success = "Lote cadastrado com sucesso";
                session()->flash("success", $success);

                return redirect()->back();
            }
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }  
    }

    public function page_manifest(){
        $produtos = VariaveisProduto::join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome as produto_nome", "variaveis_produtos.id","variaveis_produtos.variavel_nome")
        ->orderBy("produto_nome", "asc")->get();

        $manifestos = Manifesto::
        join("variaveis_produtos", "manifestos.variavel_produto_id", "=", "variaveis_produtos.id")
        ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("manifestos.*", "produtos.nome", "variaveis_produtos.variavel_nome")
        ->orderBy("id", "desc")
        ->get();

        return view("site/admin/estoque/manifestos/index", compact("produtos", "manifestos"));
    }

    public function new_manifest(Request $request){
        try{
            $dados = $request->except("_token");
            $variavel = VariaveisProduto::where("id", $dados["variavel_produto_id"])->first();

            if($dados["acao"] == "Remover"){
                $variavel->variavel_quantidade -= $dados["quantidade"];
            } else {
                $variavel->variavel_quantidade += $dados["quantidade"];
            }

            $dados["data"] = now();

            DB::transaction(function() use ($variavel, $dados){
                $variavel->save();
                Manifesto::create($dados);
            });

            $success = "Manifesto criado com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
}
