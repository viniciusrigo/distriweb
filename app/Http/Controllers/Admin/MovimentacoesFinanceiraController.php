<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MovimentacoesFinanceira;
use App\Models\Pedido;
use Exception;

class MovimentacoesFinanceiraController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth");
    }

    public function page_index(){
        $movs_fin_e = MovimentacoesFinanceira::orderBy("data", "desc")
        ->join("forma_pagamentos", "movimentacoes_financeiras.forma_pagamento_id", "forma_pagamentos.id")
        ->join("local_vendas", "movimentacoes_financeiras.local_id", "local_vendas.id")
        ->select(
            "movimentacoes_financeiras.*",
            "forma_pagamentos.nome as pagamento_nome",
            "forma_pagamentos.taxa",
            "local_vendas.local"
        )->take(250)
        ->get();

        $pedidos = Pedido::all("frete", "data");

        return view("site/admin/financeiro/movimentacoes/index", compact("movs_fin_e", "pedidos"));
    }
}
