@extends('adminlte::page')

@section('title', 'DW - Contas a Pagar')

@section('content_header')

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@endsection

@section('content')
    @if (session('excluida-success'))  
        <div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
            <span style="color: #ffffff;" class="icon-alert fa-solid fa-circle-check"></span>
            <span style="color: #ffffff;" class="msg">{{ session('excluida-success') }}</span>
        </div>
    @endif
    @if (session('paga-success'))  
        <div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
            <span style="color: #ffffff;" class="icon-alert fa-solid fa-circle-check"></span>
            <span style="color: #ffffff;" class="msg">{{ session('paga-success') }}</span>
        </div>
    @endif
    @if (session('cadastro-success'))  
        <div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
            <span style="color: #ffffff;" class="icon-alert fa-solid fa-circle-check"></span>
            <span style="color: #ffffff;" class="msg">{{ session('cadastro-success') }}</span>
        </div>
    @endif
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Nova conta à pagar</h3>
        </div>

        <div class="card-body">
            <form class="form-row" action="{{ route('admin.financeiro.contas-a-pagar.store') }}" method="POST">
                @csrf
                <input type="hidden" id="data_criacao" name="data_criacao" value="@php echo date('Y-m-d')@endphp">       
                <div class="form-group col-sm-2" style="padding: 3px;">
                    <label for="conta_id" style="margin: 0px;">Conta<code>*</code>&nbsp;&nbsp;&nbsp;<a style="color: #28A745;" href=""><i class="fa fa-plus-circle" aria-hidden="true"></i></a></label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="conta_id" name="conta_id" required>
                        <option value="">Escolha...</option>
                        <option value="1">Água</option>
                        <option value="2">Luz</option>
                        <option value="3">Internet</option>
                    </select>
                </div>
                <div class="form-group col-sm-2" style="padding: 3px;">
                    <label for="fornecedor_id" style="margin: 0px;">Fornecedor<code>*</code>&nbsp;&nbsp;&nbsp;<a style="color: #28A745;" href=""><i class="fa fa-plus-circle" aria-hidden="true"></i></a></label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="fornecedor_id" name="fornecedor_id" required>
                        <option value="">Escolha...</option>
                        <option value="1">Sanepar</option>
                        <option value="2">Copel</option>
                        <option value="3">Sercomtel</option>
                    </select>
                </div>
                <div class="form-group col-sm-2" style="padding: 3px;">
                    <label for="vencimento" style="margin: 0px;">Vencimento<code>*</code></label>
                    <input type="date" style="margin: 0px;" class="form-control form-control-border border-width-2" id="vencimento" name="vencimento" required>
                </div>
                <div class="form-group col-sm-1" style="padding: 3px;">
                    <label for="valor" style="margin: 0px;">Valor<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="valor" name="valor" required>
                </div>
                <div class="form-group col-sm-2" style="padding: 3px;">
                    <label for="status" style="margin: 0px;">Status<code>*</code></label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="status" name="status" required>
                        <option value="a">Aguardando</option>
                        <option value="p">Pago</option>
                    </select>
                </div>
                <div class="d-flex form-group col-sm-12">
                    <button style="margin-left: auto;" class="btn btn-success" type="submit">Cadastrar</button>
                </div>    
            </form>
        </div>
        
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                    <div class="card-header">
                        {{-- <h3 style="color: #007BFF;" class="card-title"><strong>Contas à Pagar</strong></h3>
                        <h3 class="card-title ml-3">
                            <a style="background-color: #28A745; color: white; padding: 3px; border-radius: 3px;" href="{{ route('admin.financeiro.contas-a-pagar.create') }}">Novo</a>
                        </h3> --}}
                        <span style="cursor:default;" class="badge badge-danger text-danger">.</span><strong style="cursor:default;"> Vencida  </strong><span style="cursor:default;" class="badge badge-warning text-warning">.</span><strong style="cursor:default;"> Vence Hoje  </strong><span style="cursor:default;" class="badge badge-primary text-primary">.</span><strong style="cursor:default;"> Vence em 3 dias  </strong>
                    </div> 

                    <table id="tabela_contas" class="table hover compact">
                        <thead>
                            <tr>
                                <th style="text-align:center">ID</th>
                                <th>Conta</th>
                                <th>Fornecedor</th>
                                <th>Vencimento</th>
                                <th style="text-align:left">Valor</th>
                                <th>Status</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contas as $conta)
                                <tr>
                                    <td style="text-align:center">{{ $conta->id }}</td>
                                    <td>{{ $conta->tipo_conta }}</td>
                                    <td>{{ $conta->nome }}</td>
                                    @php
                                        $hoje = date('Y-m-d');
                                        $vencimentoSegundos = strtotime($conta->vencimento);
                                        $hojeSegundos = time();
                                        $diferenca = ($vencimentoSegundos - $hojeSegundos);
                                        if($conta->status == 'a') {
                                            if($diferenca < 0){
                                                echo '<td><span class="badge badge-danger text-white">'.date("d/m/Y", strtotime($conta->vencimento)).'</span></td>';
                                            }elseif ($diferenca > 0 && $diferenca < 86400){
                                                echo '<td><span class="badge badge-warning text-white">'.date("d/m/Y", strtotime($conta->vencimento)).'</span></td>';
                                            }elseif ($diferenca > 86400 && $diferenca < 259200) {
                                                echo '<td><span class="badge badge-primary text-white">'.date("d/m/Y", strtotime($conta->vencimento)).'</span></td>';
                                            }else {
                                                echo '<td><span class="badge badge-white text-dark">'.date("d/m/Y", strtotime($conta->vencimento)).'</span></td>';
                                            }
                                        }else {
                                            echo '<td><span class="badge badge-success text-white">'.date("d/m/Y", strtotime($conta->vencimento)).'</span></td>';
                                        }
                                    @endphp     
                                    <td style="text-align:left">R${{ $conta->valor }}</td>
                                    @if ($conta->status == 'a')
                                        <td><span class="badge badge-warning text-white">{{ $conta->status == 'a' ? 'Aguardando':'' }}</span></td>
                                        <td>
                                            <form style="display: inline;" action="contas-a-pagar/pagar/{{ $conta->id }}" method="GET">
                                                @csrf
                                                <button style="border: none;" class="badge badge-success">Pagar</button>
                                            </form>
                                            <form style="display: inline;" action="contas-a-pagar/delete/{{ $conta->id }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button style="border: none;" class="badge badge-danger">Excluir</button>
                                            </form>
                                        </td>
                                    @endif                                        
                                </tr> 
                            @endforeach                               
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>

                    <table id="tabela_contas_pagas" class="table hover compact">
                        <thead>
                            <tr>
                                <th style="text-align:center">ID</th>
                                <th>Conta</th>
                                <th>Fornecedor</th>
                                <th>Vencimento</th>
                                <th>Data Pagamento</th>
                                <th style="text-align:left">Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contasPagas as $contaPaga)
                                <tr>
                                    <td style="text-align:center">{{ $contaPaga->id }}</td>
                                    <td>{{ $contaPaga->tipo_conta }}</td>
                                    <td>{{ $contaPaga->nome }}</td>
                                    <td>@php echo date("d/m/Y", strtotime($contaPaga->vencimento)) @endphp</td>
                                    <td>@php echo date("H:i:s d/m/Y", strtotime($contaPaga->data_pagamento)) @endphp</td>
                                    <td style="text-align:left">R${{ $contaPaga->valor }}</td>
                                    <td><span class="badge badge-success text-white">{{ $contaPaga->status == 'p' ? 'Pago':'' }}</span></td>           
                                </tr> 
                            @endforeach                               
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>         
                </div>     
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            new DataTable('#tabela_contas', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                pagingType: 'first_last_numbers',
                order: [[2, 'asc']],
                scrollCollapse: true,
                scrollY: '300px',
            });
            new DataTable('#tabela_contas_pagas', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                order: [[3, 'desc']],
                paging: false,
                scrollCollapse: true,
                scrollY: '300px',
            });

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
        })
    </script>
@stop