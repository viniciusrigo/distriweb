@extends('adminlte::page')

@section('title', 'DW - Config. Global')

@section('content_header')

@stop

@section('css')
    <style>

    </style> 
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Dados da Empresa</h3>
        </div>
        
        <div class="card-body">
            <form class="form-row" action="{{ route('admin.estoque.store') }}" method="POST">
                @csrf
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="nome" style="margin: 0px;">CNPJ<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="nome" name="nome" required>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="promocao" style="margin: 0px;">Tipo de Tributação</label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="promocao" name="promocao">
                        <option value="1">Lucro Real</option>
                        <option value="2">Lucro Presumido</option>
                        <option value="3">Simples Nacional</option>
                    </select>
                </div>
                <div class="form-group col-md-4" style="padding: 3px;">
                    <label for="preco" style="margin: 0px;">Razão Social<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco" name="preco" required>
                </div>
                <div class="form-group col-md-4" style="padding: 3px;">
                    <label for="preco_custo" style="margin: 0px;">Nome Fantasia<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco_custo" name="preco_custo" required>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="preco_promocao" style="margin: 0px;">Inscrição Estadual</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco_promocao" name="preco_promocao">
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="quantidade" style="margin: 0px;">Telefone<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="quantidade" name="quantidade" required>
                </div>
                <div class="form-group col-md-12"><strong style="color: #007BFF;">Endereço</strong></div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="sku" style="margin: 0px;">CEP</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="sku" name="sku">
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="pontos" style="margin: 0px;">Logradouro</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="pontos" name="pontos">
                </div>
                
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="cfop" style="margin: 0px;">Número</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cfop" name="cfop">
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="ncm" style="margin: 0px;">Complemento</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="ncm" name="ncm">
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="cst_csosn" style="margin: 0px;">Bairro</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_csosn" name="cst_csosn">
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="cst_pis" style="margin: 0px;">Cidade</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_pis" name="cst_pis">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="cst_cofins" style="margin: 0px;">UF</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_cofins" name="cst_cofins">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="cst_ipi" style="margin: 0px;">IBGE</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_ipi" name="cst_ipi">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="perc_icms" style="margin: 0px;">DDD</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_icms" name="perc_icms">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="perc_pis" style="margin: 0px;">SIAFI</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_pis" name="perc_pis">
                </div>
                <div class="form-group col-md-12"><strong style="color: #007BFF;">Emissão</strong></div>
                <div class="form-group col-md-4" style="padding: 3px;">
                    <label for="perc_cofins" style="margin: 0px;">CSC</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_cofins" name="perc_cofins">
                </div>
                <div class="form-group col-md-4" style="padding: 3px;">
                    <label for="perc_ipi" style="margin: 0px;">CSC ID</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_ipi" name="perc_ipi">
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="ult_compra" style="margin: 0px;">Ambiente</label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="promocao" name="promocao">
                        <option value="1">Homologação</option>
                        <option value="2">Produção</option>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    NFe
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número de série<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro">
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última NFe(Produção)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro">
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última NFe(Homologação)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro">
                </div>
                <div class="form-group col-md-12">
                    NFCe
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número de série<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro">
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última NFCe(Produção)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro">
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última NFCe(Homologação)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro">
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="promocao" style="margin: 0px;">Nat. de Oper. para PDV</label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="promocao" name="promocao">
                        <option value="1">Venda</option>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    CTe
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número de série<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro">
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última CTe(Produção)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro">
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última CTe(Homologação)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro">
                </div>
                <div class="form-group col-md-12"><strong style="color: #007BFF;">Certificado A1</strong></div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label>Certificado A1</label>
                    <input type="file" class="form-control-file" name="" id="">
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Senha<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro">
                </div>
                <div class="d-flex form-group col-md-12">
                    <button style="margin-left: auto;" class="btn btn-success" type="submit">Cadastrar</button>
                </div>
            </form>
        </div> 
    </div>
@stop