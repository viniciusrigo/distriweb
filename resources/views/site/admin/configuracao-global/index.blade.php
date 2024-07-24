@extends('adminlte::page')

@section('title', 'DW - Config. Global')

@section('content_header')

@stop

@section('css')
    <style>

    </style> 
@stop

@section('content')
    <div class="modal fade" id="novo-banco" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title">Novo Banco</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-banco" class="d-flex justify-content-center row" action="{{ route('admin.configuracao-global.novo-banco') }}" method="POST">
                        @csrf
                        <input class="form-control col-5" type="text" name="nome" placeholder="Nome do Banco" required>
                        <input class="form-control col-5" type="text" name="agencia" placeholder="Agência" required>
                        <input class="form-control col-5" type="text" name="conta" placeholder="Conta" required>
                        <input class="form-control col-5" type="text" name="saldo" placeholder="Saldo Inicial/Atual" required>
                    </form>
                </div>
                <div class="modal-footer">
                    <button form="form-banco" type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="nova-forma" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title">Nova Forma de Pagamento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-pagamento" class="d-flex justify-content-center row" action="{{ route('admin.configuracao-global.nova-forma-pagamento') }}" method="POST">
                        @csrf
                        <input class="form-control col-5" type="text" name="nome" placeholder="Nome" required>
                        <input class="form-control col-5" type="text" name="taxa" placeholder="Taxa" required>
                        <select class="form-control col-5" name="banco_id" placeholder="Conta" required >
                            <option value="">Banco</option>
                            @foreach ($bancos as $banco)
                                @if ($banco->id != 1)    
                                    <option value="{{ $banco->id }}">{{ $banco->nome }}</option>
                                @endif
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="modal-footer">
                    <button form="form-pagamento" type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="novo-tipo-conta" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title">Novo Tipo de Conta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-tipo-conta" class="d-flex justify-content-center row" action="{{ route('admin.configuracao-global.novo-tipo-conta') }}" method="POST">
                        @csrf
                        <input class="form-control col-5" type="text" name="tipo_conta" placeholder="Nome" required>
                    </form>
                </div>
                <div class="modal-footer">
                    <button form="form-tipo-conta" type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center row mb-1">
        <div class="d-flex justify-content-center col-12 mt-2">
            <div class="col-12 col-md-3 p-1">
                <div class="card card-success collapsed-card">
                    <div class="card-header p-2">
                        <h3 class="card-title">Bancos</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div style="height: 150px; overflow: auto" class="card-body p-2">
                        <div class="d-flex justify-content-center">
                            <table class="table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Nome</th>
                                        <th scope="col">Ag</th>
                                        <th scope="col">Conta</th>
                                        <th scope="col">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bancos as $banco)
                                        <tr>
                                            <td>{{ $banco->nome }}</td>
                                            <td>{{ $banco->agencia }}</td>
                                            <td>{{ $banco->conta }}</td>
                                            <td>#</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-1">
                            <button class="btn-sm btn-success" data-toggle="modal" data-target="#novo-banco">Novo</button>
                        </div>
                    </div>            
                </div>
            </div>
            <div class="col-12 col-md-3 p-1">
                <div class="card card-success collapsed-card">
                    <div class="card-header p-2">
                        <h3 class="card-title">Formas Pagamentos</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div style="height: 150px; overflow: auto" class="card-body p-2">
                        <div class="d-flex justify-content-center">
                            <table class="table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Nome</th>
                                        <th scope="col">Taxa</th>
                                        <th scope="col">Banco</th>
                                        <th scope="col">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($forma_pagamentos as $forma_pagamento)
                                        <tr>
                                            <td>{{ $forma_pagamento->nome }}</td>
                                            <td>{{ $forma_pagamento->taxa }}%</td>
                                            <td>{{ $forma_pagamento->banco_nome }}</td>
                                            <td>#</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-1">
                            <button type="submit" class="btn-sm btn-success" data-toggle="modal" data-target="#nova-forma">Novo</button>
                        </div>
                    </div>            
                </div> 
            </div>
            <div class="col-12 col-md-4 p-1">
                <div class="card card-success collapsed-card">
                    <div class="card-header p-2">
                        <h3 class="card-title">Locais</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div style="height: 150px; overflow: auto" class="card-body p-2">
                        <div class="d-flex justify-content-center">
                            <div style="display: none;">
                                <form id="locais" action="{{ route('admin.configuracao-global.store') }}" method="POST">
                                    @csrf
                                </form>
                            </div>
                            <table class="table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Nome</th>
                                        <th scope="col">Crédito</th>
                                        <th scope="col">Débito</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($locais as $local)
                                        @if ($local->id != 1)
                                            <tr>
                                                <td>{{ $local->local }}</td>
                                                <td>
                                                    <select style="border: none;" form="locais" name="{{ $local->local }}-credito">
                                                        <option value=""></option>
                                                        @foreach ($forma_pagamentos as $forma_pagamento)
                                                            @if ($forma_pagamento->id != '1' && $forma_pagamento->id != '2' && $forma_pagamento->id != '3')
                                                                <option value="{{ $forma_pagamento->id }}" {{ $local->credito_id == $forma_pagamento->id ? 'selected' : '' }}>{{ $forma_pagamento->nome }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select style="border: none;" form="locais" name="{{ $local->local }}-debito">
                                                        <option value=""></option>
                                                        @foreach ($forma_pagamentos as $forma_pagamento)
                                                            @if ($forma_pagamento->id != '1' && $forma_pagamento->id != '2' && $forma_pagamento->id != '3')
                                                                <option value="{{ $forma_pagamento->id }}" {{ $local->debito_id == $forma_pagamento->id ? 'selected' : '' }}>{{ $forma_pagamento->nome }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-1">
                            <button form="locais" type="submit" class="btn-sm btn-success">Salvar</button>
                        </div>
                    </div>            
                </div> 
            </div>
            <div class="col-12 col-md-2 p-1">
                <div class="card card-success collapsed-card">
                    <div class="card-header p-2">
                        <h3 class="card-title">Tipos de Conta</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div style="height: 150px; overflow: auto" class="card-body p-2">
                        <div class="d-flex justify-content-center">
                            <table class="table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Tipo de Conta</th>
                                        <th scope="col">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tipo_contas as $conta)
                                        <tr>
                                            <td>{{ $conta->tipo_conta }}</td>
                                            <td>#</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-1">
                            <button class="btn-sm btn-success" data-toggle="modal" data-target="#novo-tipo-conta">Novo</button>
                        </div>
                    </div>            
                </div> 
            </div>
        </div>
    </div>
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Dados da Empresa</h3>
        </div>
        
        <div class="card-body">
            @if ($info_empresa["atualizar"] == true)
            <form class="form-row" action="{{ route('admin.configuracao-global.update-info-empresa') }}" method="POST">
            @else    
                <form class="form-row" action="{{ route('admin.configuracao-global.info-empresa') }}" method="POST">
            @endif
            
                @csrf
                @if ($info_empresa["atualizar"] == true)
                    @method('PUT')
                @endif
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="cnpj" style="margin: 0px;">CNPJ<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cnpj" name="cnpj" value="{{ isset($info_empresa["cnpj"]) ? $info_empresa["cnpj"] : "" }}" required>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="tipo_tributacao" style="margin: 0px;">Tipo de Tributação</label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="tipo_tributacao" name="tipo_tributacao" disabled>
                        <option value=""></option>
                        <option value="1">Lucro Real</option>
                        <option value="2">Lucro Presumido</option>
                        <option value="3">Simples Nacional</option>
                    </select>
                </div>
                <div class="form-group col-md-4" style="padding: 3px;">
                    <label for="razao_social" style="margin: 0px;">Razão Social<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="razao_social" name="razao_social" value="{{ isset($info_empresa["razao_social"]) ? $info_empresa["razao_social"] : "" }}" required>
                </div>
                <div class="form-group col-md-4" style="padding: 3px;">
                    <label for="nome_fantasia" style="margin: 0px;">Nome Fantasia<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="nome_fantasia" name="nome_fantasia" value="{{ isset($info_empresa["nome_fantasia"]) ? $info_empresa["nome_fantasia"] : "" }}" required>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="ie" style="margin: 0px;">Inscrição Estadual</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="ie" name="ie" value="{{ isset($info_empresa["ie"]) ? $info_empresa["ie"] : "" }}">
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="telefone" style="margin: 0px;">Telefone<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="telefone" name="telefone" value="{{ isset($info_empresa["telefone"]) ? $info_empresa["telefone"] : "" }}" required>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="codigo_interno" style="margin: 0px;">Código Interno Entrega<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="codigo_interno" name="codigo_interno" value="{{ isset($info_empresa["codigo_interno"]) ? $info_empresa["codigo_interno"] : "" }}" maxlength="10" required>
                </div>
                <div class="form-group col-md-12"><strong style="color: #007BFF;">Endereço</strong></div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="cep" style="margin: 0px;">CEP<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cep" name="cep"  value="{{ isset($info_empresa["cep"]) ? $info_empresa["cep"] : "" }}" required>
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="logradouro" style="margin: 0px;">Logradouro</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="logradouro" name="logradouro" value="{{ isset($info_empresa["logradouro"]) ? $info_empresa["logradouro"] : "" }}" readonly>
                </div>
                
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="numero" style="margin: 0px;">Número<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="numero" name="numero" value="{{ isset($info_empresa["numero"]) ? $info_empresa["numero"] : "" }}" required>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="complemento" style="margin: 0px;">Complemento</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="complemento" name="complemento" value="{{ isset($info_empresa["complemento"]) ? $info_empresa["complemento"] : "" }}">
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="bairro" style="margin: 0px;">Bairro</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="bairro" name="bairro" value="{{ isset($info_empresa["bairro"]) ? $info_empresa["bairro"] : "" }}" readonly>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="localidade" style="margin: 0px;">Cidade</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="localidade" name="localidade" value="{{ isset($info_empresa["localidade"]) ? $info_empresa["localidade"] : "" }}" readonly>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="uf" style="margin: 0px;">UF</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="uf" name="uf" value="{{ isset($info_empresa["uf"]) ? $info_empresa["uf"] : "" }}" readonly>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="ibge" style="margin: 0px;">IBGE</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="ibge" name="ibge" value="{{ isset($info_empresa["ibge"]) ? $info_empresa["ibge"] : "" }}" readonly>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="ddd" style="margin: 0px;">DDD</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="ddd" name="ddd" value="{{ isset($info_empresa["ddd"]) ? $info_empresa["ddd"] : "" }}" readonly>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="siafi" style="margin: 0px;">SIAFI</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="siafi" name="siafi" value="{{ isset($info_empresa["siafi"]) ? $info_empresa["siafi"] : "" }}" readonly>
                </div>
                <div class="form-group col-md-12"><strong style="color: #007BFF;">Emissão</strong></div>
                <div class="form-group col-md-4" style="padding: 3px;">
                    <label for="perc_cofins" style="margin: 0px;">CSC</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_cofins" name="perc_cofins" disabled>
                </div>
                <div class="form-group col-md-4" style="padding: 3px;">
                    <label for="perc_ipi" style="margin: 0px;">CSC ID</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_ipi" name="perc_ipi" disabled>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="ult_compra" style="margin: 0px;">Ambiente</label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="promocao" name="promocao" disabled>
                        <option value="1">Homologação</option>
                        <option value="2">Produção</option>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    NFe
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número de série<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro" disabled>
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última NFe(Produção)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro" disabled>
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última NFe(Homologação)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro" disabled>
                </div>
                <div class="form-group col-md-12">
                    NFCe
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número de série<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro" disabled>
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última NFCe(Produção)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro" disabled>
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última NFCe(Homologação)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro" disabled>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="promocao" style="margin: 0px;">Nat. de Oper. para PDV</label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="promocao" name="promocao" disabled>
                        <option value="1">Venda</option>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    CTe
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número de série<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro" disabled>
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última CTe(Produção)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro" disabled>
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Número da última CTe(Homologação)<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro" disabled>
                </div>
                <div class="form-group col-md-12"><strong style="color: #007BFF;">Certificado A1</strong></div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label>Certificado A1</label>
                    <input type="file" class="form-control-file" name="" id="" disabled>
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Senha<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro" disabled>
                </div>
                @if ($info_empresa["atualizar"] == true)
                    <div class="d-flex form-group col-md-12">
                        <button style="margin-left: auto;" class="btn btn-success" type="submit">Atualizar</button>
                    </div>
                @else    
                    <div class="d-flex form-group col-md-12">
                        <button style="margin-left: auto;" class="btn btn-success" type="submit">Cadastrar</button>
                    </div>
                @endif
                </form>
        </div> 
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function(){
            var _token = $('meta[name="_token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': _token
                }
            });
            $('.alert').addClass("show");
            $('.alert').removeClass("hide");
            $('.alert').addClass("showAlert");
            setTimeout(function(){
                $('.alert').removeClass("show");
                $('.alert').addClass("hide");
            },3500);
        })

        $('#cep').on('blur', function() {
            var busca = this.value;
            $.get('https://viacep.com.br/ws/'+busca+'/json/', function (dados){
                $('#logradouro').val(dados.logradouro)
                $('#bairro').val(dados.bairro)
                $('#localidade').val(dados.localidade)
                $('#uf').val(dados.uf)
                $('#ibge').val(dados.ibge)
                $('#ddd').val(dados.ddd)
                $('#siafi').val(dados.siafi)
            })  
        })
    </script>
@endsection