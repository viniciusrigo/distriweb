@extends('adminlte::page')

@section('title', 'DW - Config. Global')

@section('content_header')

@stop

@section('css')
    <style>
        @media (max-width: 575.98px) {
            .off{
                display: none;
            }
        }
        @media (min-width: 1200px) {
            .off{
                display: flex;
            }
        }
        #div_dinheiro, #div_troco {
            display:none;
        }
        :focus {
            outline: 0;
            border-color: #2260ff;
            box-shadow: 0 0 0 4px #b5c9fc;
        }

        .escolha div {
            display: flex;
            flex-wrap: wrap;
            margin-top: 0.5rem;
            justify-content: center;
        }

        .escolha input[type="radio"] {
            clip: rect(0 0 0 0);
            clip-path: inset(100%);
            height: 1px;
            overflow: hidden;
            position: absolute;
            white-space: nowrap;
            width: 1px;
        }

        .escolha input[type="radio"]:checked + span {
            box-shadow: 0 0 0 0.0625em #0043ed;
            background-color: #dee7ff;
            z-index: 1;
            color: #0043ed;
        }

        label span {
            display: block;
            cursor: pointer;
            background-color: #fff;
            padding: 0.375em .75em;
            position: relative;
            margin-left: .0625em;
            box-shadow: 0 0 0 0.0625em #b5bfd9;
            letter-spacing: .05em;
            color: #3e4963;
            text-align: center;
            transition: background-color .5s ease;
        }

        label:first-child span {
            border-radius: .375em 0 0 .375em;
        }

        label:last-child span {
            border-radius: 0 .375em .375em 0;
        }
    </style>
@stop

@section('content')
    <div class="d-flex justify-content-center align-items-center row">
        <div style="font-size: 45px;" class="d-flex justify-content-center col-md-12">
            Local: PDV1
        </div>
        <div style="font-size: 30px;" class="d-flex justify-content-center col-md-12">
            <div class="d-flex justify-content-center col-md-10 bg-primary rounded-pill">
                <span class="text-primary">.</span>
                @if (session('produto'))
                    {{ session('nome') }}
                @endif
            </div>
        </div>
        <div class="d-flex justify-content-center col-md-12 mt-3">
            <div class="d-flex justify-content-center col-md-10 row">
                <div class="d-flex justify-content-center col-md-3 bg-white border border-primary rounded-pill">
                    <form class="col-md-11" id="add-produto" action="{{ route('admin.pdv.add-produto') }}" method="POST">
                        @csrf
                        <input type="hidden" name="venda_id" value="{{ Request::segment(4) }}">
                        <input type="hidden" name="local_id" value="4">
                        <input type="hidden" class="quantidade" name="quantidade" placeholder="Quantidade" value="1" readonly>
                        <input type="text" style="margin: 0px;" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" class="form-control form-control-border border-width-2" id="codigo_barras" name="codigo_barras" placeholder="Código de Barras..." autofocus>
                    </form>
                </div>
                <div class="d-flex justify-content-center col-md-2">
                    <span class="btn btn-danger mr-2" style="padding-left: 20px;padding-right: 20px;" onclick="menos()">-</span>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2 col-md-2" id="mostra-quantidade" value="1" disabled>
                    <span class="btn btn-success ml-2" style="padding-left: 20px;padding-right: 20px;" onclick="mais()">+</span>
                </div>
                <div class="d-flex justify-content-center col-md-4 bg-white border border-primary rounded-pill">
                    <form class="d-flex justify-content-center align-items-center row" action="{{ route('admin.pdv.add-produto') }}" method="POST">
                        @csrf
                        <input type="hidden" name="venda_id" value="{{ Request::segment(4) }}">
                        <input type="hidden" class="quantidade" name="quantidade" placeholder="Quantidade" value="1" readonly>
                        <div class="d-flex justify-content-center col-md-9">
                            <select name="variavel_produto_id" class="js-example-basic-single form-control form-control-border">
                                <option value=""></option>
                                @foreach ($produtos as $produto)                                        
                                    <option value="{{ $produto->id }}">{{ $produto->produto_nome }} {{ $produto->variavel_nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-center col-md-2 ml-3">
                            <button type="submit" class="btn btn-success p-1">Adicionar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-center col-md-12 mt-3">
            <div class="d-flex justify-content-center col-md-10 row">
                <div style="height: 500px" class="off justify-content-center align-items-center col-md-3 border">
                    @if (session('produto'))
                        <img src="{{ url('storage/variaveis_produtos/'.session("id").'.png') }}" alt="" width="300px" height="300px">
                    @endif  
                </div>
                <div style="height: 500px"  class="col-md-4">
                    <div style="font-size: 19px;" class="d-flex justify-content-center col-md-12">
                        Código Barras
                    </div>
                    <div style="font-size: 30px;" class="d-flex justify-content-center col-md-12 bg-primary rounded-pill">
                        <span class="text-primary">.</span>
                        @if (session('produto'))
                            {{ session('codigo_barras') }}
                        @endif
                    </div>
                    <div style="font-size: 19px;" class="d-flex justify-content-center col-md-12">
                        Valor Unitário
                    </div>
                    <div style="font-size: 30px;" class="d-flex justify-content-center col-md-12 bg-primary rounded-pill">
                        R$
                        @if (session('produto'))
                            {{ session('preco') }}
                        @endif
                    </div>
                    <div style="font-size: 19px;" class="d-flex justify-content-center col-md-12">
                        Pontos Ganhos
                    </div>
                    <div style="font-size: 30px;" class="d-flex justify-content-center col-md-12 bg-primary rounded-pill">
                        <span>Pontos: </span><span><strong>{{ $venda[0]->pontos }}</strong></span>
                    </div>
                    <div style="font-size: 19px;" class="d-flex justify-content-center col-md-12">
                        Adicional ao Valor Total
                    </div>
                    <div class="d-flex justify-content-center row col-md-12">
                        <div style="font-size: 30px;" class="d-flex justify-content-center col-md-6 bg-white border border-primary rounded-pill">
                            <form id="form-adicional" action="{{ route('admin.pdv.adicional-venda') }}" method="POST">
                                @csrf
                                <input type="hidden" name="venda_id" value="{{ Request::segment(4) }}">
                                <input class="form-control form-control-border border-width-2 col-md-12" name="adicional" type="text" placeholder="Valor Adicional R$" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                            </form>
                        </div>
                        <div class="d-flex justify-content-center col-md-3">
                            <button form="form-adicional" type="button" class="btn btn-success col-md-12 rounded-pill" data-toggle="modal" data-target="#staticBackdrop">Adicionar</button>
                        </div>
                    </div>
                    <div style="font-size: 19px;" class="d-flex justify-content-center col-md-12">
                        Forma de Pagamento
                    </div>
                    <div style="font-size: 20px;" class="d-flex justify-content-center col-md-12">
                        <form id="concluir" class="d-flex justify-content-center flex-wrap" action="{{ route('admin.pdv.concluir-venda') }}" method="POST">
                            @csrf
                            <input type="hidden" name="venda_id" value="{{ Request::segment(4) }}">
                            <div class="d-inline form-check" style="padding: 0px;">
                                <div class="escolha">
                                    <div>
                                        <label>
                                            <input type="radio" name="pagamento" onclick="aparecer(0)" id="pix" value="3" required>
                                            <span>PIX</span>
                                        </label>
                                        <label>
                                            <input type="radio" name="pagamento" onclick="aparecer(1)" id="dinheiro" value="2" required>
                                            <span>Dinheiro</span>
                                        </label>                                  
                                        <label>
                                            <input type="radio" name="pagamento" onclick="aparecer(0)" id="credito" value="credito" required>
                                            <span>Crédito</span>
                                        </label>                                  
                                        <label>
                                            <input type="radio" name="pagamento" onclick="aparecer(0)" id="debito" value="debito" required>
                                            <span>Débito</span>
                                        </label>                                  
                                        <label>
                                            <input type="radio" name="pagamento" onclick="aparecer(0)" id="Ticket" value="Ticket" required>
                                            <span>Ticket</span>
                                        </label>                                  
                                    </div>
                                </div>
                            </div>
                            <div class="justify-content-center col-md-12" id="div_dinheiro" style="padding: 3px;">
                                <input type="text" style="margin: 0px;height: 2em;font-size: 25px;" class="form-control form-control-border border-width-2 col-md-4" id="input_dinheiro" name="dinheiro" placeholder="R$0.00" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7">
                                <input type="text" style="margin: 0px;height: 2em;font-size: 25px;" class="form-control form-control-border border-width-2 col-md-4" id="input_troco" name="troco" placeholder="Troco" readonly>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="col-md-12">
                        <div class="card">      
                            <div style="height: 500px" class="card-body table-responsive p-0">
                                <table class="table table-sm text-nowrap text-dark">
                                        @if (count($carrinho) > 0)
                                            @foreach ($carrinho as $item)
                                                <tr>
                                                    <td>{{ $item->codigo_barras == null ? "Sem código barras" : $item->codigo_barras }}</td>
                                                    <td>{{ $item->produto_nome }} {{ $item->variavel_nome }}</td>
                                                    @if ($item->promocao == "s")
                                                        <td>R${{ $item->preco_promocao }}</td>
                                                    @else
                                                        <td>R${{ $item->preco }}</td>
                                                    @endif
                                                    <td>
                                                        <form style="display: inline;" action="{{ route('admin.pdv.remove-produto') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="venda_id" value="{{ Request::segment(4) }}">
                                                            <input type="hidden" name="variavel_produto_id" value="{{ $item->variavel_produto_id }}">
                                                            <button type="submit" style="border: none;" class="badge badge-danger"><i class="fa fa-times-circle" aria-hidden="true"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach     
                                        @endif                                
                                    </tbody>
                                </table>
                            </div>  
                        </div>                    
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center col-md-12 mt-1">
            <div class="d-flex justify-content-center col-md-10 row">
                <div class="col-md-3">
                    @if (count($carrinho) > 0)
                        <div class="d-flex justify-content-center col-12">
                            <input id="venda_id" type="hidden" name="venda_id" value="{{ Request::segment(4) }}">
                            <button style="font-size: 16px;" onclick="imprimir()" class="btn btn-info col-12 rounded-pill"><i class="fa-solid fa-print"></i> Imprimir</button>
                        </div>
                    @endif
                </div>
                <div class="col-md-4">
                    <div style="font-size: 26px;" class="d-flex justify-content-center col-md-12 bg-primary rounded-pill">
                        Total:R$<input class="bg-primary" style="width: 130px; border: none; color: black;" type="text" name="total" id="input-total" value="{{ $venda[0]->valor }}" disabled>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="d-flex justify-content-center col-md-12 bg-white border border-primary rounded-pill">
                        <form class="col-md-11" action="{{ route('admin.pdv.remove-produto') }}" method="POST">
                            @csrf
                            <input type="hidden" name="venda_id" value="{{ Request::segment(4) }}">
                            <input type="text" style="margin: 0px;" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" class="form-control form-control-border border-width-2" id="codigo_barras_remover" name="codigo_barras_remover" placeholder="Remover produto..." autofocus>
                        </form>
                    </div>
                    <div class="col-md-12">
                        
                    </div>
                </div>
            </div>
        </div>
        @if (count($carrinho) == 0)
            <div class="d-flex justify-content-center col-10 mt-1">
                <form class="col-12" action="{{ route('admin.pdv.delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="venda_id" value="{{ Request::segment(4) }}">
                    <button style="margin: 0px;height: 2em;font-size: 25px;" class="btn btn-danger rounded-pill col-12" type="submit">Cancelar</button>
                </form>
            </div>
        @else
            <div class="d-flex justify-content-center col-10 mt-1">
                <button style="margin: 0px;height: 2em;font-size: 25px;" form="concluir" class="btn btn-success rounded-pill col-md-12" type="submit"><i class="fas fa-solid fa-cart-plus"></i> Finalizar Compra</button>   
            </div>
        @endif

        <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                    <div class="modal-body row">
                        <span class="text-warning col-md-12 d-flex justify-content-center"><strong><i class="fa-solid fa-triangle-exclamation"></i>  Atenção</strong></span>
                        <span style="font-size: 20px;" class="col-md-12 d-flex justify-content-center">Ao realizar essa operação não tera como desfaze-la, quer continuar?</span>
                    </div>
                    <div class="modal-footer justify-content-center">
                    <button form="form-adicional" type="submit" class="btn btn-success">Sim</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function imprimir(){
            let venda = $('#venda_id').val();
            let aba = window.open("http://localhost:8989/admin/pdv/venda/imprimir/"+venda)
            setTimeout(function(){
                aba.print()
            }, 0250);
            setTimeout(function(){
                aba.close()
            }, 2500);
        }

        $(document).ready(function(){
            var _token = $('meta[name="_token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': _token
                }
            });

            document.getElementById("div_dinheiro").style.display = "none";
            $.fn.select2.defaults.set("theme", "classic");
            $(".js-example-basic-single").select2({
                placeholder: 'Selecione um produto',
                allowClear: true
            });

            $("#input_dinheiro").blur(function(){
                let valor = parseFloat($(this).val())
                let total = parseFloat($("#input-total").val())
                if(valor < total){
                    $("#input_dinheiro").val("")
                    $("#input_troco").val("")
                }
            })

            $('.alert').addClass("show");
            $('.alert').removeClass("hide");
            $('.alert').addClass("showAlert");
            setTimeout(function(){
                $('.alert').removeClass("show");
                $('.alert').addClass("hide");
            },3500);
        })

        function mais(){
            var add = parseFloat($('#mostra-quantidade').val()) + 1
            $('#mostra-quantidade').val(add)
            $('.quantidade').val(add)
        }
        function menos(){
            if($('#mostra-quantidade').val() > 0) {
                var add = parseFloat($('#mostra-quantidade').val()) - 1
                $('#mostra-quantidade').val(add)
                $('.quantidade').val(add)
            }
            if ($('#mostra-quantidade').val() == 0) {
                $('#mostra-quantidade').val(1)
                $('.quantidade').val(1)
            }
        }

        function calcular() {
            var n1 = parseFloat($("#input_dinheiro").val(), 4, 2);
            var n2 = parseFloat(document.getElementById("input-total").value, 4);
            document.getElementById("input_troco").value = (n1 - n2).toFixed(2);
        }

        function aparecer(n){
            if(n == 1){
                document.getElementById("div_dinheiro").style.display = "flex";
            } else {
                document.getElementById("div_dinheiro").style.display = "none";
            }
        }
        document.getElementById("input_dinheiro").onkeyup = function(){
            calcular();
        }

    </script>
@stop