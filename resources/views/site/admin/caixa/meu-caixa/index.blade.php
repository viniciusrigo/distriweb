@extends('adminlte::page')

@section('title', 'DW - Caixa')

@section('content_header')

@stop

@section('css')
    <style>

    </style>
@stop

@section('content')
    <div class="d-flex justify-content-center row">
        @if (isset($caixa_aberto))
        <div class="col-md-11">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-6 col-md-3 mt-1">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-1"><i class="fa-solid fa-flag-checkered"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Saldo Inicial</span>
                            <span class="info-box-number">
                                R${{ $caixa_aberto->valor_inicial }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3 mt-1">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fa-solid fa-spinner"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Saldo Atual</span>
                            <span class="info-box-number">
                                R${{ $banco->saldo }}
                            </span>
                        </div>
                    </div> 
                </div>
            </div>
        @endif
    
        <div class="col-12">
            @if (isset($caixa_aberto))
                <div class="card table-responsive p-0">
                    <div class="card-header d-flex justify-content-center p-1">
                        <strong>Fluxo de Caixa</strong>
                    </div>       
                    <div class="card-body p-3">
                        <div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="tabela-fluxo" class="table hover compact">
                                        <thead>
                                            <tr>
                                                <th style="text-align:left"><span class="badge badge-warning text-white">#</span></th>
                                                <th style="text-align:left"><span class="badge badge-warning text-white">Venda</span></th>
                                                <th style="text-align:left"><span class="badge badge-success">Entrada</span></th>
                                                <th style="text-align:left"><span class="badge badge-danger">Saída</span></th>
                                                <th style="text-align:left"><span class="badge badge-info">Data</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @isset($fluxo)
                                                @foreach ($fluxo as $fluxo)
                                                    <tr>
                                                        <td style="text-align:left">{{ $fluxo["id"] }}</td>
                                                        <td style="text-align:left">R${{ $fluxo["venda"] }}</td>
                                                        <td style="text-align:left">R${{ $fluxo["dinheiro"] }}</td>
                                                        <td style="text-align:left">R${{ $fluxo["troco"] }}</td>
                                                        <td style="text-align:left">{{ date('H:i:s d/m/Y', strtotime($fluxo["data"])) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endisset
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{ route('admin.caixa.close') }}" method="POST"> 
                    @csrf
                    <input type="hidden" name="status" value="f">
                    <input type="hidden" name="saldo_atual" value="{{ $banco->saldo }}">
                    <div class="card table-responsive p-0">
                        <input type="text" name="valor_retirada" class="form-control col-md-3 ml-auto mr-auto" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44"  maxlength="7" placeholder="Digite valor de retirada" required>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-box-open"></i>
                            FECHAR CAIXA
                        </button>
                    </div>
                </form> 
            @else
                <form action="{{ route('admin.caixa.open') }}" method="POST" class="mt-4"> 
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="data_abertura" value="{{ now() }}">
                    <div class="card table-responsive p-0">
                        @if (isset($ult_caixa))
                            @if ($ult_caixa->valor_final > 0)
                                <input type="hidden" name="valor_inicial" class="form-control col-md-3 ml-auto mr-auto" value="{{ $ult_caixa->valor_final }}">
                            @else
                                <input type="text" name="valor_inicial" class="form-control col-md-3 ml-auto mr-auto" placeholder="Digite valor inicial do caixa" autofocus required>
                            @endif
                        @else
                            <input type="text" name="valor_inicial" class="form-control col-md-3 ml-auto mr-auto" placeholder="Digite valor inicial do caixa" autofocus required>
                        @endif
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-box-open"></i>
                            ABRIR CAIXA
                        </button>
                    </div>
                </form> 
            @endif
        </div>
        <div class="col-12">
            <div class="card table-responsive p-0">      
                <div class="card-body p-2">
                    <div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="tabela-caixas_fechados" class="table hover compact">
                                    <thead>
                                        <tr>
                                            <th style="text-align:left">#</th>
                                            <th style="text-align:left">Resp</th>
                                            <th style="text-align:left">V. Inicial</th>
                                            <th style="text-align:left">Retirada</th>
                                            <th style="text-align:left">V. Final</th>
                                            <th>Abertura</th>
                                            <th>Fechamento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($caixas)
                                            @foreach ($caixas as $caixa)
                                                <tr >
                                                    <td style="text-align:left">{{ $caixa["id"] }}</td>
                                                    <td style="text-align:left">{{ $caixa["user_id"] }}</td>
                                                    <td style="text-align:left">R${{ $caixa["valor_inicial"] }}</td>
                                                    <td style="text-align:left">R${{ $caixa["valor_retirada"] }}</td>
                                                    <td style="text-align:left">R${{ $caixa["valor_final"] }}</td>
                                                    <td>{{ date('H:i:s d/m/Y', strtotime($caixa["data_abertura"])) }}</td>
                                                    <td>{{ date('H:i:s d/m/Y', strtotime($caixa["data_fechamento"])) }}</td>
                                                </tr>
                                            @endforeach
                                        @endisset
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

            new DataTable('#tabela-fluxo', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                order: [[0, 'desc']],
                paging: false,
                scrollCollapse: true,
                scrollY: '300px',
                searching: false
            });

            new DataTable('#tabela-caixas_fechados', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                pagingType: 'first_last_numbers',
                order: [[3, 'asc']],
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