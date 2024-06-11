@extends('adminlte::page')

@section('title', 'DW - Estoque')

@section('content_header')

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@stop

@section('content')
    @if (session('success'))
    <div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
        <span style="color: #ffffff;" class="fas fa-solid fa-circle-check"></span>
        <span style="color: #ffffff;" class="msg">{{ session('success') }}</span>
    </div>
    @endif
    @if (session('alert'))
    <div style="background: #ffdb9b; border-left: 8px solid #ffa502;" class="alert hide">
        <span style="color: #ce8500;" class="fas fa-exclamation-circle"></span>
        <span style="color: #ce8500;" class="msg">{{ session('alert') }}</span>
    </div>
    @endif
    @if (session('error')) 
    <div style="background: #ff9b9b; border-left: 8px solid #ff0202;" class="alert hide">
        <span style="color: #ce0000;" class="fas fa-solid fa-xmark"></span>
        <span style="color: #ce0000;" class="msg">{{ session('error') }}</span>
    </div>
    @endif
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-6 col-md-3 mt-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fa-solid fa-flag-checkered"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Saldo Inicial</span>
                        <span class="info-box-number">
                            @if (isset($caixa_aberto))
                                R${{ $caixa_aberto->valor_inicial }}
                            @else
                                NULL
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mt-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Saldo Atual</span>
                        <span class="info-box-number">
                            @if (isset($caixa_aberto))
                                R${{ $fluxo->sum('venda') + $caixa_aberto->valor_inicial }}
                            @else
                                NULL
                            @endif
                        </span>
                    </div>
                </div> 
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center row">
        <div class="col-md-8">
            @if (isset($caixa_aberto))
            <div class="card table-responsive p-0">
                <div class="card-header">
                    Fluxo de Caixa
                </div>       
                <div class="card-body">
                    <table id="tabela-fluxo" class="table hover compact">
                        <thead>
                            <tr>
                                <th><span class="badge badge-warning text-white">Venda</span></th>
                                <th><span class="badge badge-success">Entrada</span></th>
                                <th><span class="badge badge-danger">Saída</span></th>
                                <th style="text-align:right"><span class="badge badge-info">Data</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($fluxo)
                                @foreach ($fluxo as $fluxo)
                                    <tr>
                                        <td>R${{ $fluxo["venda"] }}</td>
                                        <td>R${{ $fluxo["dinheiro"] }}</td>
                                        <td>R${{ $fluxo["troco"] }}</td>
                                        <td style="text-align:right">{{ date('H:i:s d/m/Y', strtotime($fluxo["data"])) }}</td>
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
            <form action="{{ route('admin.caixa.close') }}" method="POST"> 
                @csrf
                <input type="hidden" name="status" value="f">
                <input type="hidden" name="saldo_atual" value="{{ $fluxo->sum('venda') + $caixa_aberto->valor_inicial }}">
                <div style="box-shadow: 0px 5px 20px #888888;" class="card table-responsive p-0">
                    <input type="text" name="valor_retirada" class="form-control col-md-3 ml-auto mr-auto" placeholder="Digite valor de retirada" required>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-box-open"></i>
                        FECHAR CAIXA
                    </button>
                </div>
            </form> 
            @else
                <form action="{{ route('admin.caixa.open') }}" method="POST"> 
                    @csrf
                    <input type="hidden" name="users_id" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="data_abertura" value="{{ now() }}">
                    <div style="box-shadow: 0px 5px 20px #888888;" class="card table-responsive p-0">
                        <input type="text" name="valor_inicial" class="form-control col-md-3 ml-auto mr-auto" placeholder="Digite valor inicial do caixa" autofocus required>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-box-open"></i>
                            ABRIR CAIXA
                        </button>
                    </div>
                </form> 
            @endif
        </div>
    </div>
@stop

@section('js')
    <script>
        
        $(document).ready(function() {

            new DataTable('#tabela-fluxo', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                order: [[3, 'desc']],
                paging: false,
                scrollCollapse: true,
                scrollY: '300px',
                searching: false
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