<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProdutosVenda;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProdutosComanda;
use App\Models\ProdutosPedido;
use App\Models\VariaveisProduto;
use App\Models\Venda;

class VendaController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function page_index(){
        $vendas = Venda::where("status", "f")
        ->join("forma_pagamentos", "vendas.forma_pagamento_id", "=", "forma_pagamentos.id")
        ->join("local_vendas", "vendas.local_id", "=", "local_vendas.id")
        ->select(
            "vendas.*",
            "forma_pagamentos.nome as pagamento_nome",
            "local_vendas.local"
        )->get();

        return view("site/admin/vendas/index", compact("vendas"));
    }

    public function detail_sale(string|int $id){
        $venda = Venda::join("forma_pagamentos", "vendas.forma_pagamento_id", "=", "forma_pagamentos.id")
        ->join("local_vendas", "vendas.local_id", "=", "local_vendas.id")
        ->select("vendas.*", "forma_pagamentos.nome as forma_pagamento", "local_vendas.local")
        ->where("vendas.id", $id)
        ->first();

        if($venda->comanda_id == null && $venda->pedido_id == null){
            $produtos = ProdutosVenda::where("venda_id", $id)
            ->join("produtos", "produtos_vendas.produto_id", "=", "produtos.id")
            ->join("variaveis_produtos", "produtos_vendas.variavel_produto_id", "=", "variaveis_produtos.id")
            ->select("produtos.nome", "variaveis_produtos.codigo_barras","variaveis_produtos.variavel_nome")
            ->get()->toArray();
        } else if($venda->pedido_id != null) {
            $produtos = ProdutosPedido::where("pedido_id", $venda->pedido_id)
            ->join("produtos", "produtos_pedidos.produto_id", "=", "produtos.id")
            ->join("variaveis_produtos", "produtos_pedidos.variavel_produto_id", "=", "variaveis_produtos.id")
            ->select("produtos.nome", "variaveis_produtos.codigo_barras","variaveis_produtos.variavel_nome")
            ->get()->toArray();
        } else {
            $produtos = ProdutosComanda::where("comanda_id", $venda->comanda_id)
            ->join("produtos", "produtos_comandas.produto_id", "=", "produtos.id")
            ->join("variaveis_produtos", "produtos_comandas.variavel_produto_id", "=", "variaveis_produtos.id")
            ->select("produtos.nome", "variaveis_produtos.codigo_barras","variaveis_produtos.variavel_nome")
            ->get()->toArray();
        }
        

        return view("site/admin/vendas/detalhe", compact("venda","produtos"));
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

}
