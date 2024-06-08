@extends('adminlte::page')

@section('title', 'DW - Config. Global')

@section('content_header')

@stop

@section('css')
    <style>
        #div_dinheiro, #div_troco {
            display:none;
        }

    </style>
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@stop

@section('content')
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
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div style="font-size: 25px;" class="col-md-6">
                <div class="card card-outline card-success col-md-12 h-25">
                    <form action="{{ route('vendedor.PDV.add-produto') }}" method="POST">
                        @csrf
                        <label for="codigo_barras" style="margin: 0px;">Código de Barras</label>
                        <input type="hidden" name="vendas_id" value="{{ Request::segment(4) }}">
                        <input type="text" style="margin: 0px;" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" class="form-control form-control-border border-width-2" id="codigo_barras" name="codigo_barras" placeholder="Buscar produto..." autofocus>
                    </form>
                </div>
                <div class="card card-outline card-success col-md-12 mt-3">
                    <div class="d-flex">
                        <span>Desconto</span><span class="ml-auto"><strong>R$0.00</strong></span>
                    </div>
                </div>
                <div class="card card-outline card-success col-md-12 mt-3">
                    <div class="d-flex">
                        <span>Pontos</span><span class="ml-auto"><strong>{{ $venda[0]->pontos }} Pontos</strong></span>
                    </div>
                </div>
                <div style="font-size: 45px;" class="card card-outline card-success col-md-12 mt-3">
                    <div class="d-flex">
                        <span>TOTAL</span>
                        <div style="display: inline; margin-left: auto;">
                            <span>R$</span>
                            <input style="width: 200px; border: none; background-color: white; color: black;" type="text" name="total" id="total" value="{{ $venda[0]->valor }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12" style="padding: 3px;">
                    <form class="d-flex flex-wrap" action="{{ route('vendedor.PDV.concluir-venda') }}" method="POST">
                        @csrf
                        <input type="hidden" name="vendas_id" value="{{ Request::segment(4) }}">
                        <div class="d-inline form-check" style="padding: 3px;">
                            <input type="radio" name="pagamento" class="outros" id="outros" value="1" ><span class="badge badge-success ml-1">PIX</span>
                            <input type="radio" name="pagamento" class="outros" id="outros" value="2"><span class="badge badge-warning ml-1 text-white">Crédito</span>
                            <input type="radio" name="pagamento" class="outros" id="outros" value="3"><span class="badge badge-primary ml-1">Débito</span>
                            <input type="radio" name="pagamento" class="dinheiro" id="dinheiro" value="4"><span class="badge badge-danger ml-1">Dinheiro</span>
                        </div>
                        <div class="col-md-2" id="div_dinheiro" style="padding: 3px;">
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="input_dinheiro" name="dinheiro" placeholder="R$0.00">
                        </div>
                        <div class="col-md-2" id="div_troco" style="padding: 3px;">
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="input_troco" name="troco" placeholder="Troco">
                        </div>
                        <button class="btn btn-success col-md-12" type="submit"><i class="fas fa-solid fa-cart-plus"></i></button>      
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="col-12">
                    <div class="card">      
                        <div class="card-body table-responsive p-0" style="height: 60vh;">
                            {{-- @php
                                echo round(5 * (3.15 / 100), 2) - 5;
                            @endphp --}}
                            <table class="table table-sm text-nowrap">
                                    @if (count($carrinho) > 0)
                                        @foreach ($carrinho as $item)
                                            <tr>
                                                <td>{{ $item->codigo_barras }}</td>
                                                <td>{{ $item->nome }}</td>
                                                @if ($item->promocao == "s")
                                                    <td>R${{ $item->preco_promocao }}</td>
                                                @else
                                                    <td>R${{ $item->preco }}</td>
                                                @endif
                                            </tr>
                                        @endforeach     
                                    @endif                                
                                </tbody>
                            </table>
                        </div>  
                    </div>                    
                </div>
            </div>
            <div class="col-md-12 d-flex">
                <div class="d-flex col-md-6">
                    <span>Nome: <strong>JM Oliveira Bebidas LTDA</strong></span>
                    <div style="margin-left: auto;" class="d-inline">
                        <span class="">Data <strong id="time"></strong></span>
                    </div>
                </div>
                <div class="col-md-6 ml-auto">
                    <form action="{{ route('vendedor.PDV.remove-produto') }}" method="POST">
                        @csrf
                        <label for="codigo_barras_remover" style="margin: 0px;">Remover Produto</label>
                        <input type="hidden" name="vendas_id" value="{{ Request::segment(4) }}">
                        <input type="text" style="margin: 0px;" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" class="form-control form-control-border border-width-2" id="codigo_barras_remover" name="codigo_barras_remover" placeholder="Remover produto..." autofocus>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>

        $(document).ready(function(){
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

        var timeDisplay = document.getElementById("time");
        function refreshTime() {
            var dateString = new Date().toLocaleString("pt-BR", {timeZone: "America/Sao_Paulo"});
            var formattedString = dateString.replace(", ", " - ");
            timeDisplay.innerHTML = formattedString;
        }
        setInterval(refreshTime, 1000);

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
        document.getElementById("outros").onclick = function(){
            document.getElementById("div_dinheiro").style.display = "none";
            document.getElementById("div_troco").style.display = "none";
        }
        document.getElementById("outros").onclick = function(){
            document.getElementById("div_dinheiro").style.display = "none";
            document.getElementById("div_troco").style.display = "none";
        }
        document.getElementById("outros").onclick = function(){
            document.getElementById("div_dinheiro").style.display = "none";
            document.getElementById("div_troco").style.display = "none";
        }
    </script>
@stop