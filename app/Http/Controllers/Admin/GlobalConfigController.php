<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\FormaPagamento;
use App\Models\GlobalConfig;
use App\Models\LocalVenda;
use App\Models\TipoConta;
use App\Models\Zona;
use Exception;

class GlobalConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }
    
    public function page_index(){
        $bancos = Banco::all("id", "nome", "agencia", "conta");
        $forma_pagamentos = FormaPagamento::join("bancos", "forma_pagamentos.banco_id", "=", "bancos.id")->select(
            "forma_pagamentos.id", "forma_pagamentos.nome", "forma_pagamentos.taxa", "bancos.nome as banco_nome"
        )->get();
        $tipo_contas = TipoConta::all("tipo_conta");
        $locais = LocalVenda::all();
        if(count(GlobalConfig::all()) > 0){
            $info_empresa = GlobalConfig::find(1)->toArray();
        } else {
            $info_empresa["atualizar"] = false;
        }

        foreach($info_empresa as $dado){
            if($dado == null || $dado == ""){
                continue;
            } else {
                $info_empresa["atualizar"] = true;
                break;
            }
        }

        $zonas = Zona::all();

        return view("site/admin/configuracao-global/index", compact("bancos", "forma_pagamentos", "tipo_contas", "locais", "info_empresa", "zonas"));
    }

    public function store(Request $request){
        try{
            $req = $request->except("_token", "Contas_a_Pagar-credito", "Contas_a_Pagar-debito");

            $dados[0] = ["c" => $req["Online-credito"], "d" => $req["Online-debito"]];
            $dados[1] = ["c" => $req["Comanda-credito"], "d" => $req["Comanda-debito"]];
            $dados[2] = ["c" => $req["PDV1-credito"], "d" => $req["PDV1-debito"]];
            $dados[3] = ["c" => $req["PDV2-credito"], "d" => $req["PDV2-debito"]];
            
            $locais = LocalVenda::where("id", "!=", 1)->get();
            
            for($i = 0; $i < count($locais); $i++){
                $locais[$i]->credito_id = $dados[$i]["c"];
                $locais[$i]->debito_id = $dados[$i]["d"];
                $locais[$i]->save();
            }

            $success = "Pagamentos dos locais de venda configurados";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function new_bank(Request $request){
        try{
            $dados = $request->except("_token");
            $dados["saldo"] = str_replace(",", ".", $dados["saldo"]);

            Banco::create($dados);

            $success = "Banco cadastrado com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function new_payment_method(Request $request){
        try{
            $dados = $request->except("_token");
            $dados["taxa"] = str_replace(",", ".", $dados["taxa"]);

            FormaPagamento::create($dados);
            
            $success = "Forma de pagamento cadastrada com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function new_account_type(Request $request){
        try{
            $dados = $request->except("_token");
            
            TipoConta::create($dados);

            $success = "Tipo de conta cadastrada com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function info_company(Request $request){
        try{
            $dados_request = $request->except("_token");

            GlobalConfig::create($dados_request);

            $success = "Informações salvas com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function update_info_company(Request $request){
        try{
            $dados_request = $request->except("_token");
            
            $empresa = GlobalConfig::find(1);
            $empresa->update($dados_request);

            $success = "Informações atualizadas com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function update_taxa(Request $request){
        try{
            $dados_request = $request->except("_token");
            $dados_request["nova_taxa"] = str_replace(",", ".", $dados_request["nova_taxa"]);

            $forma_pagamento = FormaPagamento::find($dados_request["forma_pagamento_id"]);
            $forma_pagamento->taxa = $dados_request["nova_taxa"];
            $forma_pagamento->save();            

            $success = "Taxa atualizada com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function update_zona(Request $request){
        try{
            $dados_request = $request->except("_token");
            $dados_request["nova_entrega"] = str_replace(",", ".", $dados_request["nova_entrega"]);
            
            $zona = Zona::find($dados_request["zona_id"])->update(["entrega" => $dados_request["nova_entrega"], "tempo_entrega" => $dados_request["novo_tempo_entrega"]]);         

            $success = "Zona atualizada com sucesso";
            session()->flash("success", $success);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
}