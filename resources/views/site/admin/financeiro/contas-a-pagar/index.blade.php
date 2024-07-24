@extends('adminlte::page')

@section('title', 'DW - Contas a Pagar')

@section('content_header')

@stop

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-center row mb-1">

        <div class="col-12">
            <div class="row p-2 justify-content-center">
                <div style="border-radius:6px" class="d-flex col-12 bg-white justify-content-center m-1 p-2">
                    <form action="{{ route('admin.financeiro.contas-a-pagar.store') }}" method="POST">
                        @csrf
                        <input type="hidden" id="data_criacao" name="data_criacao" value="@php echo date('Y-m-d')@endphp">       
                        <div class="row justify-content-center">
                            <div class="m-1 col-2">
                                <label for="conta_id" style="margin: 0px;">Conta<code>*</code></label>
                                <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="conta_id" name="conta_id" required>
                                    <option value="">Escolha...</option>
                                    @foreach ($tipos_contas as $tc)
                                        <option value="{{ $tc->id }}">{{ $tc->tipo_conta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="m-1 col-2">
                                <label for="fornecedor_id" style="margin: 0px;">Fornecedor<code>*</code></label>
                                <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="fornecedor_id" name="fornecedor_id" required>
                                    <option value="">Escolha...</option>
                                    @foreach ($fornecedores as $fornecedores)
                                        <option value="{{ $fornecedores->id }}">{{ $fornecedores->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="m-1 col-2">
                                <label for="banco_id" style="margin: 0px;">Banco<code>*</code></label>
                                <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="banco_id" name="banco_id" required>
                                    <option value="">Escolha...</option>
                                    @foreach ($bancos as $banco)
                                        <option value="{{ $banco->id }}">{{ $banco->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="m-1">
                                <label for="vencimento" style="margin: 0px;">Vencimento<code>*</code></label>
                                <input type="date" style="margin: 0px;" class="form-control form-control-border border-width-2" id="vencimento" name="vencimento" required>
                            </div>
                            <div class="m-1" style="width: 65px">
                                <label for="valor" style="margin: 0px;">Valor<code>*</code></label>
                                <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="valor" name="valor" placeholder="R$" required>
                            </div>
                            <div class="m-1 col-2">
                                <label for="status" style="margin: 0px;">Status<code>*</code></label>
                                <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="status" name="status" required>
                                    <option value="a">Aguardando</option>
                                    <option value="p">Pago</option>
                                </select>
                            </div>   
                        </div>
                        <div class="d-flex m-1">
                            <button class="btn btn-success ml-auto mr-auto" type="submit">Cadastrar</button>
                        </div> 
                    </form>
                </div>
            </div>
        </div>


        <div class="col-12">
            <div class="card table-responsive p-0">
                <div class="card-body p-2">
                    <div>
                        <div class="row">
                            <div class="col-12">
                                    <div class="card-header p-1">
                                        <span style="cursor:default;" class="badge badge-danger text-danger">.</span><strong style="cursor:default;"> Vencida  </strong><span style="cursor:default;" class="badge badge-warning text-warning">.</span><strong style="cursor:default;"> Vence Hoje  </strong><span style="cursor:default;" class="badge badge-primary text-primary">.</span><strong style="cursor:default;"> Vence em 3 dias  </strong>
                                    </div> 
                                    <table id="tabela_contas" class="table hover compact">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center">ID</th>
                                                <th>Conta</th>
                                                <th>Fornecedor</th>
                                                <th>Banco</th>
                                                <th>Vencimento</th>
                                                <th style="text-align:left">Valor</th>
                                                <th>Status</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($contas as $conta)
                                                <tr class="tb-tr-bd">
                                                    <td style="text-align:center">{{ $conta->id }}</td>
                                                    <td>{{ $conta->tipo_conta }}</td>
                                                    <td>{{ $conta->nome }}</td>
                                                    <td>{{ $conta->banco_nome }}</td>
                                                    @php
                                                        $hoje = date('Y-m-d 00:00:01');
                                                        $vencimentoSegundos = strtotime($conta->vencimento);
                                                        $hojeSegundos = strtotime($hoje);
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
                                                                <input type="hidden" name="valor" value="{{$conta->valor}}">
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
                                                <th>Banco</th>
                                                <th>Vencimento</th>
                                                <th>Data Pagamento</th>
                                                <th style="text-align:left">Valor</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($contasPagas as $contaPaga)
                                                <tr class="tb-tr-bd">
                                                    <td style="text-align:center">{{ $contaPaga->id }}</td>
                                                    <td>{{ $contaPaga->tipo_conta }}</td>
                                                    <td>{{ $contaPaga->nome }}</td>
                                                    <td>{{ $contaPaga->banco_nome }}</td>
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
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>         
                                </div>     
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            var _token = $('meta[name="_token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': _token
                }
            });

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
            },3500);
        })
    </script>
@stop