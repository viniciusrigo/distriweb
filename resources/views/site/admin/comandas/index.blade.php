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
        .tb-tr-bd {
            -webkit-transition: -webkit-transform .05s ease;
            transition: transform .05s ease;
        }
        .tb-tr-bd:hover {
            -webkit-transform: scale(1.02);
            transform: scale(1.01);
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
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="nome" name="nome" required>
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
    

    <div class="d-flex justify-content-center row">
        <div class="col-12">
            <div class="row p-2 justify-content-center">
                <div style="border-radius:6px" class="d-flex col-12 bg-white justify-content-center m-1 p-2">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#abrirComanda">
                        Abrir Comanda
                    </button>
                </div>
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
                                            <table style="cursor: default;" class="table-sm text-nowrap">                                  
                                                <tbody>
                                                    @foreach ($comanda->produtos as $produtos)
                                                        <tr>
                                                            <td>{{$produtos->quantidade}}x</td>
                                                            <td>{{ $produtos->nome }}</td>
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
                        <div class="card-footer d-flex justify-content-center row bg-white p-0">
                            {{-- Verifica se a comanda está aberta --}}
                            @if ($comanda->status == 'a')
                                <div class="col-12">
                                    <span class="badge badge-white"><i class="fa fa-circle text-success" aria-hidden="true"></i> <strong>Comanda Aberta</strong></span>
                                </div>
                                {{-- Verifica se a comanda tem produtos --}}
                                @if (!count($comanda->produtos) == 0)
                                    <form id="fechar" class="d-flex flex-wrap col-12" style="display: inline; margin-left: auto;" action="{{ route('admin.comandas.closed') }}" method="POST">
                                        @csrf
                                        <div class="col-12 form-check p-1">
                                            <input type="radio" name="pagamento" class="outros" id="pix" value="3" ><label for="pix" class="badge badge-success ml-1">PIX</label>
                                            <input type="radio" name="pagamento" class="outros" id="credito" value="credito"><label for="credito" class="badge badge-warning ml-1 text-white">Crédito</label>
                                            <input type="radio" name="pagamento" class="outros" id="debito" value="debito"><label for="debito" class="badge badge-primary ml-1">Débito</label>
                                            <input type="radio" name="pagamento" class="dinheiro" id="dinheiro" value="2"><label for="dinheiro" class="badge badge-danger ml-1">Dinheiro</label>
                                        </div>
                                        <div class="justify-content-center col-12 p-0" id="div_dinheiro" style="padding: 3px;">
                                            <div class="col-5">
                                                <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="input_dinheiro" name="dinheiro" placeholder="R$0.00">
                                            </div>
                                            <div class="col-5">
                                                <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="input_troco" name="troco" placeholder="Troco">
                                            </div>
                                        </div>
                                        <input type="hidden" name="id" value="{{ $comanda->id }}">
                                        <input type="hidden" name="status" value="f">
                                    </form>
                                    <form id="imprimir" action="comandas/imprimir/{{ $comanda->id }}" method="GET" target="_blank"></form>
                                    <div class="d-flex justify-content-center col-12 p-2">
                                        <button form="fechar" style="border: none;" class="badge badge-danger p-2">Fechar comanda</button>
                                        <button form="imprimir" style="border: none;" class="badge badge-info p-2 ml-3"><i class="fa-solid fa-print"></i></button>
                                    </div>   
                                @endif
                                <div class="d-flex justify-content-center">
                                    <span>Total: <strong>R$</strong></span>
                                    <input style="width: 70px; font-weight:bold; border: none; outline: none; color: black; background-color: white;" id="total" type="text" value="{{ $comanda->total }}" disabled>
                                </div>
                            @else
                                <span class=""><i class="fa fa-circle text-danger" aria-hidden="true"></i> <strong>Fechada</strong></span>
                                <span class="ml-auto">Total: <strong>R${{ $comanda->total }}</strong></span>
                            @endif                     
                            <span style="margin-left: auto;">Abertura: <strong>{{ $comanda->data_abertura }}</strong></span>
                        </div>  
                    </div>
                @endforeach
                {{-- @endisset --}}
            </div>
        </div>

        <div class="col-12">
            <div class="card table-responsive p-0">
                <div class="card-header">
                    <h3 class="card-title"><strong>Comandas Fechadas</strong></h3>
                </div>
                <div class="card-body p-2">
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
                                                <td style="text-align:left">{{ $fechada->id }}</td>
                                                <td style="text-align:left">{{ $fechada->nome }}</td>
                                                <td style="text-align:left">R${{ $fechada->total }}</td>
                                                <td><span style='padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-brands fa-pix'></i>  <strong>{{$fechada->pagamento_nome}}</strong></span></td> 
                                                <td><span style='background-color: #ee9292; color: #5e1b1b; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Fechada</strong></span></td>
                                                <td style="text-align:left">@php echo date("H:i:s d/m/y", strtotime($fechada->data_abertura)) @endphp</td>
                                                <td style="text-align:left">@php echo date("H:i:s d/m/y", strtotime($fechada->data_fechamento)) @endphp</td>
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
            document.getElementById("div_dinheiro").style.display = "flex";
            document.getElementById("div_troco").style.display = "flex";
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