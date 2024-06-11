@extends('adminlte::page')

@section('title', 'DW - Comandas')

@section('content_header')

@stop

@section('css')
    <style>
        @keyframes sAtivo {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 0; }
        }
        .sAtivo {
            -webkit-animation: sAtivo 1.5s linear infinite;
            -moz-animation: sAtivo 1.5s linear infinite;
            -ms-animation: sAtivo 1.5s linear infinite;
            -o-animation: sAtivo 1.5s linear infinite;
            animation: sAtivo 1.5s linear infinite;
        }
        #div_dinheiro, #div_troco {
            display:none;
        }
        input[type=radio]  {
            border: 0px;
            width: 13px;
            height: 17px;
            vertical-align: middle;
        }
    </style> 
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@stop

@section('content')
    @if (session('success'))
        <div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
            <span style="color: #ffffff;" class="icon-alert fa-solid fa-circle-check"></span>
            <span style="color: #ffffff;" class="msg">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error')) 
        <div style="background: #ff9b9b; border-left: 8px solid #ff0202;" class="alert hide">
            <span style="color: #ce0000;" class="fas fa-solid fa-xmark"></span>
            <span style="color: #ce0000;" class="msg">{{ session('error') }}</span>
         </div>
    @endif
    <div class="modal fade" id="abrirComanda" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <form class="form-row" action="{{ route('admin.comandas.store') }}" method="POST">
                        @csrf
                        <div class="form-group col-md-5 ml-auto mr-auto" style="padding: 3px;">
                            <label for="nome" style="margin: 0px;">Responsável<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="nome" name="nome">
                        </div>
                        <div class="form-group col-md-12" style="padding: 3px;">
                            <div class="col-md-2 mr-auto ml-auto">
                                <button type="submit" class="btn btn-success m-0">
                                    Abrir
                                </button>
                            </div>             
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-success card-outline">
        <div class="card-body">
            <button type="button" class="btn-sm btn-success" data-toggle="modal" data-target="#abrirComanda">
                Abrir Comanda
            </button>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            {{-- @isset($comandas) --}}
            @foreach ($comandas as $comanda)
                @if ($comanda->status == 'a')
                    <div class="card card-outline card-success col-md-3 mr-1">
                @else
                    <div class="card card-outline card-danger col-md-3">
                @endif
                    <div class="card-header">
                        <h3 class="card-title"><strong>{{ $comanda->nome }}</strong></h3>
                        <div class="card-tools">
                            @if ($comanda->status == 'f')
                                <span>Fechada <strong>{{ $comanda->data_fechamento }}</strong></span>
                            @else
                                @if (count($comanda->produtos) == 0)
                                    <form style="display: inline; margin-left: auto;" action="comandas/delete/{{ $comanda->id }}" method="GET">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $comanda->id }}">
                                        <button style="border: none;" class="badge badge-danger" id="excluir_comanda">Excluir</button>
                                    </form>
                                @endif                               
                            @endif 
                        </div>
                    </div>   
                    <div class="card-body p-0" style="display: block;">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">      
                                    <div class="card-body table-responsive p-0" style="height: 200px;">
                                        <table style="cursor: default;" class="table table-sm text-nowrap">                                  
                                            <tbody>
                                                @foreach ($comanda->produtos as $produtos)
                                                    <tr>
                                                        <td>{{ $produtos->nome }}</td>
                                                        <td>R${{ $produtos->preco }}</td>
                                                        <td>@php echo date('H:i:s', strtotime($produtos->data_compra)); @endphp</td>
                                                        <td>
                                                            <form style="display: inline;" action="{{ route('admin.comandas.remove-produto') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $produtos->id }}">
                                                                <input type="hidden" name="comandas_id" value="{{ $comanda->id }}">
                                                                <input type="hidden" name="data_compra" value="{{ $produtos->data_compra }}">
                                                                <button type="submit" style="border: none;" class="badge badge-danger"><i class="fa fa-times-circle" aria-hidden="true"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach    
                                            </tbody>
                                        </table>
                                        <div>

                                        </div>
                                    </div>  
                                </div>                    
                            </div>
                        </div>
                    </div>
                    <div class="card-footer row p-0">
                        <form class="ml-auto mr-auto" action="{{ route('admin.comandas.add-produto') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $comanda->id }}">
                            <input type="text" style="margin: 0px;" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13"  class="form-control rounded-0 " id="codigo_barras" name="codigo_barras" placeholder="Código de barras">
                        </form>
                    </div>
                    <div class="card-footer row bg-white">
                        {{-- Verifica se a comanda está aberta --}}
                        @if ($comanda->status == 'a')
                            <form style="display: inline;cursor: default;">
                                <span class="badge badge-white"><i class="sAtivo fa fa-circle text-success" aria-hidden="true"></i> <strong>Comanda Aberta</strong></span>
                            </form>
                            {{-- Verifica se a comanda tem produtos --}}
                            @if (!count($comanda->produtos) == 0)
                                <form class="d-flex row" style="display: inline; margin-left: auto;" action="{{ route('admin.comandas.closed') }}" method="POST">
                                    @csrf
                                    <div class="d-block form-check" style="padding: 3px;" >
                                        <input type="radio" name="pagamento" class="outros" id="pix" value="1" ><span class="badge badge-success ml-1">PIX</span>
                                        <input type="radio" name="pagamento" class="outros" id="credito" value="2"><span class="badge badge-warning ml-1 text-white">Crédito</span>
                                        <input type="radio" name="pagamento" class="outros" id="debito" value="3"><span class="badge badge-primary ml-1">Débito</span>
                                        <input type="radio" name="pagamento" class="dinheiro" id="dinheiro" value="4"><span class="badge badge-danger ml-1">Dinheiro</span>
                                    </div>
                                    <div class="col-md-5" id="div_dinheiro" style="padding: 3px;">
                                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="input_dinheiro" name="dinheiro" placeholder="R$0.00">
                                    </div>
                                    <div class="col-md-5 ml-auto" id="div_troco" style="padding: 3px;">
                                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="input_troco" name="troco" placeholder="Troco">
                                    </div>
                                    <input type="hidden" name="id" value="{{ $comanda->id }}">
                                    <input type="hidden" name="status" value="f">
                                    <button style="border: none; margin-left:auto; margin-right: auto;" class="badge badge-danger p-2">Fechar comanda</button>
                                </form>   
                            @endif
                            <span>Total: <strong>R$</strong></span>
                            <input style="width: 70px; font-weight:bold; border: none; outline: none; color: black; background-color: white;" id="total" type="text" value="{{ $comanda->total }}" disabled>
                        @else
                            <span class=""><i class="fa fa-circle text-danger" aria-hidden="true"></i> <strong>Fechada</strong></span>
                            <span class="ml-auto">Total: <strong>R${{ $comanda->total }}</strong></span>
                        @endif                     
                        <span style="margin-left: auto;"><strong>{{ $comanda->data_abertura }}</strong></span>
                    </div>  
                </div>
            @endforeach
            {{-- @endisset --}}
        </div>
    </div>
    <div class="row">
        <div class="card col-md-12">
            <div class="card-header">
                <h3 class="card-title"><strong>Comandas Fechadas</strong></h3>
            </div>
            <div class="card-body">
                <div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="tabela_comandas_fechadas" class="hover compact">
                                <thead>
                                    <tr>
                                        <th style="text-align:left" rowspan="1" colspan="1">Nº</th>
                                        <th rowspan="1" colspan="1">Nome</th>
                                        <th style="text-align:left" rowspan="1" colspan="1">Total</th>
                                        <th style="text-align:left" rowspan="1" colspan="1">Pagamento</th>
                                        <th rowspan="1" colspan="1">Status</th>
                                        <th rowspan="1" colspan="1">Abertura</th>
                                        <th rowspan="1" colspan="1">Fechamento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($comandas_fechadas as $fechada)
                                        <tr class="tb-tr-bd">
                                            <td style="text-align:left"><strong>{{ $fechada->id }}</strong></td>
                                            <td style="text-align:left"><strong>{{ $fechada->nome }}</strong></td>
                                            <td style="text-align:left">
                                                <strong>R${{ $fechada->total }}
                                                    @php
                                                        if($fechada->taxa > 0){
                                                            echo "<span class='badge badge-danger'>";
                                                            echo "<i class='fa-solid fa-arrow-trend-down'></i>  R$";
                                                            echo round(($fechada->total * 100) / (100 - $fechada->taxa) - $fechada->total, 2);
                                                            echo "</span>";
                                                        }
                                                    @endphp
                                                </strong>
                                            </td>
                                            @php
                                                if ($fechada->forma_pagamentos_id == 1) {
                                                    echo "<td><span style='background-color: #514ab3; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-brands fa-pix'></i>  <strong>PIX</strong></span></td>";
                                                } else if ($fechada->forma_pagamentos_id == 2) {
                                                    echo "<td><span style='background-color: #949238; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-solid fa-credit-card'></i>  Crédito</span></td>";
                                                } else if ($fechada->forma_pagamentos_id == 3) {
                                                    echo "<td><span style='background-color: #ee9292; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-solid fa-credit-card'></i>  Débito</span></td>";
                                                } else {
                                                    echo "<td><span style='background-color: #92d8ee; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-solid fa-money-bill-1'></i>  Dinheiro</span></td>";
                                                }
                                            @endphp 
                                            <td><span style='background-color: #ee9292; color: #5e1b1b; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Fechada</strong></span></td>
                                            <td style="text-align:left"><strong>@php echo date("H:i:s d/m/y", strtotime($fechada->data_abertura)) @endphp</strong></td>
                                            <td style="text-align:left"><strong>@php echo date("H:i:s d/m/y", strtotime($fechada->data_fechamento)) @endphp</strong></td>
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
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function(){
            new DataTable('#tabela_comandas_fechadas', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                pagingType: 'first_last_numbers',
                order: [[0, 'desc']],
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

        function calcular() {
            var n1 = parseFloat(document.getElementById('input_dinheiro').value, 4, 2);
            var n2 = parseFloat(document.getElementById('total').value, 4);
            document.getElementById('input_troco').value = (n1 - n2).toFixed(2);
        }
        document.getElementById("input_dinheiro").onkeyup = function(){
            calcular();
        }

        document.getElementById("dinheiro").onclick = function(){
            document.getElementById("div_dinheiro").style.display = "inline";
            document.getElementById("div_troco").style.display = "inline";
        }
        document.getElementById("pix").onclick = function(){
            document.getElementById("div_dinheiro").style.display = "none";
            document.getElementById("div_troco").style.display = "none";
        }
        document.getElementById("credito").onclick = function(){
            document.getElementById("div_dinheiro").style.display = "none";
            document.getElementById("div_troco").style.display = "none";
        }
        document.getElementById("debito").onclick = function(){
            document.getElementById("div_dinheiro").style.display = "none";
            document.getElementById("div_troco").style.display = "none";
        }
    </script>
@stop