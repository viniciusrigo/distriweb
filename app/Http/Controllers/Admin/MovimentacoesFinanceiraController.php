<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MovimentacoesFinanceira;
use Exception;

class MovimentacoesFinanceiraController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $movs_fin_e = MovimentacoesFinanceira::orderBy("data", "desc")->join("forma_pagamentos", "movimentacoes_financeiras.forma_pagamentos_id", "forma_pagamentos.id")->select(
            "movimentacoes_financeiras.id",
           "movimentacoes_financeiras.ponto_partida",
           "movimentacoes_financeiras.valor",
           "movimentacoes_financeiras.lucro",
           "movimentacoes_financeiras.forma_pagamentos_id",
           "forma_pagamentos.taxa",
           "movimentacoes_financeiras.tipo",
           "movimentacoes_financeiras.data",
        )->get();

        return view("site/admin/financeiro/movimentacoes/index", compact("movs_fin_e"));
    }
}
