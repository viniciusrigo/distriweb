@extends('adminlte::page')

@section('title', 'DW - Config. Global')

@section('content_header')

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
    <style>
        ::-webkit-scrollbar{
            width: 7px;
        }
        ::-webkit-scrollbar-thumb{
            border-radius: 30px;
            background-color: #cccccc;
        }
        ::-webkit-scrollbar-thumb:hover{
            border-radius: 30px;
            background-color: #a6a6a6;
        }
    </style> 
@stop

@section('content')

    {{-- POP UPs --}}
    @if (session('error')) 
    <div style="background: #ff9b9b; border-left: 8px solid #ff0202;" class="alert hide">
        <span style="color: #ce0000;" class="fas fa-solid fa-xmark"></span>
        <span style="color: #ce0000;" class="msg">{{ session('error') }}</span>
    </div>
    @endif
    @if (session('alerta'))
    <div style="background: #ffdb9b; border-left: 8px solid #ffa502;" class="alert hide">
        <span style="color: #ce8500;" class="fas fa-exclamation-circle"></span>
        <span style="color: #ce8500;" class="msg">{{ session('alerta') }}</span>
    </div>
    @endif
    @if (session('success'))
    <div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
        <span style="color: #ffffff;" class="fas fa-solid fa-circle-check"></span>
        <span style="color: #ffffff;" class="msg">{{ session('success') }}</span>
    </div>
    @endif

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
            <form class="form-row" action="{{ route('admin.configuracao-global.store') }}" method="POST">
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

@section('js')
    <script>
        $('.alert').addClass("show");
        $('.alert').removeClass("hide");
        $('.alert').addClass("showAlert");
        setTimeout(function(){
            $('.alert').removeClass("show");
            $('.alert').addClass("hide");
        },5000);
        $('.close-btn').click(function(){
            $('.alert').removeClass("show");
            $('.alert').addClass("hide");
        });
    </script>
@endsection