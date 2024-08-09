<?php

namespace App\Http\Controllers;

use App\Models\FluxoBanco;
use App\Models\GlobalConfig;
use App\Models\MovimentacoesFinanceira;
use App\Models\PagarConta;
use App\Models\ProdutosComanda;
use App\Models\ProdutosPedido;
use App\Models\ProdutosVenda;
use App\Models\VariaveisProduto;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("auth");
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $dataInicio = date("Y-m-01 00:00:00");
        $dataFim = date("Y-m-d H:i:s");
        $lucro_vendas = Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->sum("lucro");
        $lucro_bancos = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("mov_extra", "s")->where("tipo", "e")->sum("valor");
        $lucro = $lucro_vendas + $lucro_bancos;
        $faturamento = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("tipo", "e")->sum("valor");
        $vendas = count(Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->get("id"));
        $despesas = PagarConta::whereBetween("data_pagamento", [$dataInicio, $dataFim])->sum("valor");

        $validade = strtotime(date("Y-m-d 00:00:00"));
        $validade = strtotime("+3 month");
        $validade = date("Y-m-d 23:59:59", $validade);

        $produtos_vencimento = VariaveisProduto::whereBetween("validade", [date("Y-m-d 00:00:00"), $validade])
        ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.variavel_nome", "variaveis_produtos.variavel_quantidade", "variaveis_produtos.validade")
        ->orderBy("validade", "asc")
        ->get()
        ->toArray();

        $produtos_lucro = VariaveisProduto::join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.variavel_nome", "variaveis_produtos.variavel_quantidade", "variaveis_produtos.lucro")
        ->orderBy("lucro", "DESC")
        ->take(20)
        ->get()
        ->toArray();

        $mais_vendidos_pdv = ProdutosVenda::join("variaveis_produtos", "produtos_vendas.variavel_produto_id", "=", "variaveis_produtos.id")
        ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("variaveis_produtos.variavel_nome", "produtos.nome", DB::raw("count(*) as total"))
        ->groupBy("produtos.nome")
        ->orderBy("total", "DESC")
        ->take(20)
        ->get();

        $mais_vendidos_comandas = ProdutosComanda::join("produtos", "produtos_comandas.produto_id", "=", "produtos.id")
        ->join("variaveis_produtos", "produtos_comandas.variavel_produto_id", "=", "variaveis_produtos.id")
        ->select("variaveis_produtos.variavel_nome", "produtos.nome", DB::raw("count(*) as total"))
        ->groupBy("produtos.nome")
        ->orderBy("total", "DESC")
        ->take(20)
        ->get();

        $mais_vendidos_pedidos = ProdutosPedido::join("produtos", "produtos_pedidos.produto_id", "=", "produtos.id")
        ->join("variaveis_produtos", "produtos_pedidos.variavel_produto_id", "=", "variaveis_produtos.id")
        ->select("variaveis_produtos.variavel_nome", "produtos.nome", DB::raw("count(*) as total"))
        ->groupBy("produtos.nome")
        ->orderBy("total", "DESC")
        ->take(20)
        ->get();
        
        $produtos_parados = VariaveisProduto::orderBy("ult_compra", "asc")
        ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.variavel_nome", "variaveis_produtos.variavel_quantidade", "variaveis_produtos.ult_compra")
        ->take(20)
        ->get()
        ->toArray();

        $global_config = GlobalConfig::get(["minimo_produto"])->first() !=  null ? GlobalConfig::get(["minimo_produto"])->first()->toArray() : 10 ;

        $produtos_estoque_baixo = VariaveisProduto::join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.variavel_nome", "variaveis_produtos.variavel_quantidade", "variaveis_produtos.ult_compra")
        ->where("variavel_quantidade", "<=", $global_config == 10 ? $global_config : $global_config["minimo_produto"])
        ->take(20)
        ->get()
        ->toArray();

        $ts = strtotime("now");
        $hoje = date("d/m/Y", $ts);
        //dd($hoje);
        $ts = strtotime("-1 week");
        $semana = date("d/m/Y", $ts);
        //dd($semana);
        $mes = date("01/m/Y", $ts);
        //dd($mes);
        $ts = strtotime("-3 month");
        $trimestre = date("d/m/Y", $ts);
        //dd($trimestre);
        $ano = date("01/01/Y", $ts);
        //dd($ano);
        
        return view("admin", compact(
            "lucro", "faturamento", "vendas",
            "despesas", "produtos_vencimento", "produtos_parados",
            "mais_vendidos_pdv", "mais_vendidos_comandas", "mais_vendidos_pedidos",
            "produtos_lucro", "produtos_estoque_baixo"
        ));
    }

    function get_dados(){
        $dados = FluxoBanco::where("tipo", "e")
        ->groupBy(DB::raw('MONTH(data)'))
        ->select(DB::raw('MONTH(data) as mes'), DB::raw("SUM(valor) as faturamento"))
        ->get()
        ->toArray();

        // for($x = 0; $x < count($dados); $x++){
        //     $dados[$x]["nome"] = $dados[$x]["nome"]." ".$dados[$x]["variavel_nome"];
        //     unset($dados[$x]["variavel_nome"]);
        // }

        return response()->json($dados);
    }

    function indicadores_ajax(Request $request){
        if($request->input("data") == "hoje"){
            $dataInicio = date("Y-m-d 00:00:00");
            $dataFim = date("Y-m-d 23:59:59");
            $lucro_vendas = Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->sum("lucro");
            $lucro_bancos = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("mov_extra", "s")->where("tipo", "e")->sum("valor");
            $dados["lucro"] = $lucro_vendas + $lucro_bancos;
            $dados["faturamento"] = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("tipo", "e")->sum("valor");
            $dados["vendas"] = count(Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->get("id"));
            $dados["despesas"] = PagarConta::whereBetween("data_pagamento", [$dataInicio, $dataFim])->sum("valor");

            return response()->json($dados);
        } else if($request->input("data") == "semana") {
            $dataInicio = date("Y-m-d 00:00:00");
            $dataInicio = strtotime($dataInicio);
            $dataInicio = strtotime("-1 week");
            $dataInicio = date("Y-m-d 00:00:00", $dataInicio);;
            $dataFim = date("Y-m-d 23:59:59");
            $lucro_vendas = Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->sum("lucro");
            $lucro_bancos = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("mov_extra", "s")->where("tipo", "e")->sum("valor");
            $dados["lucro"] = $lucro_vendas + $lucro_bancos;
            $dados["faturamento"] = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("tipo", "e")->sum("valor");
            $dados["vendas"] = count(Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->get("id"));
            $dados["despesas"] = PagarConta::whereBetween("data_pagamento", [$dataInicio, $dataFim])->sum("valor");

            return response()->json($dados);
        } else if($request->input("data") == "mes") {
            $dataInicio = date("Y-m-01 00:00:00");
            $dataFim = date("Y-m-d 23:59:59");
            $lucro_vendas = Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->sum("lucro");
            $lucro_bancos = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("mov_extra", "s")->where("tipo", "e")->sum("valor");
            $dados["lucro"] = $lucro_vendas + $lucro_bancos;
            $dados["faturamento"] = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("tipo", "e")->sum("valor");
            $dados["vendas"] = count(Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->get("id"));
            $dados["despesas"] = PagarConta::whereBetween("data_pagamento", [$dataInicio, $dataFim])->sum("valor");

            return response()->json($dados);
        } else if($request->input("data") == "trimestre") {
            $dataInicio = date("Y-m-d 00:00:00");
            $dataInicio = strtotime($dataInicio);
            $dataInicio = strtotime("-3 month");
            $dataInicio = date("Y-m-d 00:00:00", $dataInicio);;
            $dataFim = date("Y-m-d 23:59:59");
            $lucro_vendas = Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->sum("lucro");
            $lucro_bancos = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("mov_extra", "s")->where("tipo", "e")->sum("valor");
            $dados["lucro"] = $lucro_vendas + $lucro_bancos;
            $dados["faturamento"] = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("tipo", "e")->sum("valor");
            $dados["vendas"] = count(Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->get("id"));
            $dados["despesas"] = PagarConta::whereBetween("data_pagamento", [$dataInicio, $dataFim])->sum("valor");

            return response()->json($dados);
        } else if($request->input("data") == "ano") {
            $dataInicio = date("Y-01-01 00:00:00");
            $dataFim = date("Y-12-31 23:59:59");
            $lucro_vendas = Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->sum("lucro");
            $lucro_bancos = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("mov_extra", "s")->where("tipo", "e")->sum("valor");
            $dados["lucro"] = $lucro_vendas + $lucro_bancos;
            $dados["faturamento"] = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("tipo", "e")->sum("valor");
            $dados["vendas"] = count(Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->get("id"));
            $dados["despesas"] = PagarConta::whereBetween("data_pagamento", [$dataInicio, $dataFim])->sum("valor");

            return response()->json($dados);
        } else if($request->input("data") == "especifico") {
            $dataInicio = date($request->input("dia")." 00:00:00");
            $dataFim = date($request->input("dia")." 23:59:59");
            $lucro_vendas = Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->sum("lucro");
            $lucro_bancos = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("mov_extra", "s")->where("tipo", "e")->sum("valor");
            $dados["lucro"] = $lucro_vendas + $lucro_bancos;
            $dados["faturamento"] = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("tipo", "e")->sum("valor");
            $dados["vendas"] = count(Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->get("id"));
            $dados["despesas"] = PagarConta::whereBetween("data_pagamento", [$dataInicio, $dataFim])->sum("valor");

            return response()->json($dados);
        } else {
            $dataInicio = date($request->input("dia_inicial")." 00:00:00");
            $dataFim = date($request->input("dia_final")." 23:59:59");
            $lucro_vendas = Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->sum("lucro");
            $lucro_bancos = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("mov_extra", "s")->where("tipo", "e")->sum("valor");
            $dados["lucro"] = $lucro_vendas + $lucro_bancos;
            $dados["faturamento"] = FluxoBanco::whereBetween("data", [$dataInicio, $dataFim])->where("tipo", "e")->sum("valor");
            $dados["vendas"] = count(Venda::whereBetween("data_venda", [$dataInicio, $dataFim])->get("id"));
            $dados["despesas"] = PagarConta::whereBetween("data_pagamento", [$dataInicio, $dataFim])->sum("valor");

            return response()->json($dados);
        }
    }
}
