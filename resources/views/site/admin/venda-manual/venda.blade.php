@extends('adminlte::page')

@section('title', 'DW - Config. Global')

@section('content_header')

@stop

@section('css')
    <style>
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
    <div class="d-flex justify-content-center">
        <div style="height:auto" class="d-flex justify-content-center row col-md-6">
            <div class="card card-outline card-success col-12 p-2 m-1">
                <form action="{{ route('admin.venda-manual.add-produto') }}" method="POST">
                    @csrf
                    <input type="hidden" name="venda_id" value="{{ Request::segment(4) }}">
                    <input type="hidden" class="quantidade" name="quantidade" placeholder="Quantidade" value="1" readonly>
                    <div class="d-flex justify-content-center align-items-center">
                        <select name="variavel_produto_id" class="js-example-basic-single form-control form-control-border col-md-7">
                            <option value=""></option>
                            @foreach ($produtos as $produto)
                                <option value="{{ $produto->id }}">{{ $produto->produto_nome }} {{ $produto->variavel_nome }}</option>
                            @endforeach
                        </select>
                        <div class="d-flex justify-content-center col-2 m-2">
                            <span class="btn btn-danger mr-2" style="padding-left: 20px;padding-right: 20px;" onclick="menos()">-</span>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2 col-4" id="mostra-quantidade" value="1" disabled>
                            <span class="btn btn-success ml-2" style="padding-left: 20px;padding-right: 20px;" onclick="mais()">+</span>
                        </div>
                        <button style="margin: 0px;height: 2em;font-size: 25px;" type="submit" class="btn btn-success ml-2">Adicionar</button>
                    </div>
                </form>
            </div>
            <div class="d-flex justify-content-center col-12">
                <div class="bg-white col-12" style="height: 30vh; overflow: auto;">
                    <table class="table table-sm text-nowrap">
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
                                            <form style="display: inline;" action="{{ route('admin.venda-manual.remove-produto') }}" method="POST">
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
            <div class="d-flex justify-content-center col-md-5 m-1">
                <form id="form-adicional" class="d-flex justify-content-center align-items-center p-2 m-1" action="{{ route('admin.venda-manual.adicional') }}" method="POST">
                    @csrf
                    <input type="hidden" name="venda_id" value="{{ Request::segment(4) }}">
                    <input style="margin: 0px;height: 2em;font-size: 25px;" class="form-control form-control-border border-width-2 col-8" name="adicional" type="text" placeholder="Valor Adicional R$" required>
                    <button style="margin: 0px;height: 2em;font-size: 25px;" type="button" class="btn btn-success ml-5" data-toggle="modal" data-target="#staticBackdrop">Adicionar</button>
                </form>
            </div>
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
            <div style="font-size: 45px;" class="card card-outline card-success col-md-12 m-1">
                <div class="d-flex mt-2">
                    <span>TOTAL</span>
                    <div style="display: inline; margin-left: auto;">
                        <span>R$</span>
                        <input style="width: 200px; border: none; background-color: white; color: black;" type="text" name="total" id="input-total" value="{{ $venda_manual[0]->valor }}" disabled>
                    </div>
                </div>
            </div>
            <div style="font-size: 25px;" class="form-group col-12">
                <form id="form-finalizar" class="d-flex justify-content-center flex-wrap" action="{{ route('admin.venda-manual.finalizar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="venda_id" value="{{ Request::segment(4) }}">
                    <div class="d-inline form-check" style="padding: 3px;">
                        <div class="escolha">
                            <div>
                                <label>
                                    <input type="radio" name="local_id" value="4" checked>
                                    <span>PDV</span>
                                </label>                                                                 
                            </div>
                        </div>
                    </div>
                    <div class="d-inline form-check" style="padding: 3px;">
                        <div class="escolha">
                            <div>
                                <label>
                                    <input type="radio" name="pagamento" onclick="aparecer(0, 'pagamento')" id="pix" value="3" required>
                                    <span>PIX</span>
                                </label>
                                <label>
                                    <input type="radio" name="pagamento" onclick="aparecer(1, 'pagamento')" id="dinheiro" value="2" required>
                                    <span>Dinheiro</span>
                                </label>                                  
                                <label>
                                    <input type="radio" name="pagamento" onclick="aparecer(0, 'pagamento')" id="credito" value="credito" required>
                                    <span>Crédito</span>
                                </label>                                  
                                <label>
                                    <input type="radio" name="pagamento" onclick="aparecer(0, 'pagamento')" id="debito" value="debito" required>
                                    <span>Débito</span>
                                </label>                                                                 
                            </div>
                        </div>
                    </div>
                    <div class="justify-content-center col-md-12" id="div_dinheiro" style="padding: 3px;">
                        <input type="text" style="margin: 0px;height: 2em;font-size: 25px;" class="form-control form-control-border border-width-2 col-2" id="input-dinheiro" name="dinheiro" placeholder="R$0.00">
                        <input type="text" style="margin: 0px;height: 2em;font-size: 25px;" class="form-control form-control-border border-width-2 col-2" id="input-troco" name="troco" placeholder="Troco" readonly>
                    </div>
                    <div class="d-flex justify-content-center col-md-12" style="padding: 3px;">
                        <input id="endereco" type="text" style="margin: 0px;height: 2em;font-size: 25px;" class="form-control form-control-border border-width-2 col-md-6" name="endereco" placeholder="Ex: Rua José, 123" required>
                        <select id="zona_id" style="margin: 0px;height: 2em;font-size: 25px;" name="zona_id" class="form-control form-control-md col-md-2 ml-2" required>
                            <option value="">Escolha...</option>
                            @foreach ($zonas as $zona)
                                <option value="{{ $zona->id }}">{{ $zona->nome }}</option>
                            @endforeach                    
                        </select>
                    </div>
                    <div style="display: none">
                        @foreach ($zonas as $zona)
                            <input type="hidden" id="zona{{$zona->id}}" value="{{ $zona->entrega }}">
                        @endforeach
                    </div>
                </form>
                @if (count($carrinho) > 0)
                    <div class="d-flex justify-content-center col-12">
                        <input id="venda_id" type="hidden" name="id" value="{{ Request::segment(4) }}">
                        <button style="margin: 0px;height: 2em;font-size: 25px;" onclick="imprimir()" class="btn btn-info col-12">Imprimir</button>
                    </div>
                @endif
            </div>
            @if (count($carrinho) == 0)
                <div class="d-flex justify-content-center col-10 mt-1">
                    <form class="col-12" action="{{ route('admin.venda-manual.delete') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ Request::segment(4) }}">
                        <button style="margin: 0px;height: 2em;font-size: 25px;" class="btn btn-danger col-12" type="submit">Cancelar</button>
                    </form>
                </div>
            @else
                <div class="d-flex justify-content-center col-10 mt-1">
                    <button style="margin: 0px;height: 2em;font-size: 25px;" form="form-finalizar" class="btn btn-success col-md-12" type="submit"><i class="fas fa-solid fa-cart-plus"></i></button>   
                </div>
            @endif
        </div>      
    </div>
@stop

@section('js')
    <script>
        
        function imprimir(){
            
            let venda_id = $('#venda_id').val();
            let endereco = $('#endereco').val();
            let zona_id = $('#zona_id').val();
            let forma_pagamento_id = $('input[name="pagamento"]:checked').val();
            let aba = "";
            if(endereco == "" || zona_id == "" || forma_pagamento_id == undefined){
               return toastr.warning("Verifique se foi preenchido ENDEREÇO/ZONA/PAGAMENTO", "Erro")
            }
            if(forma_pagamento_id == 2){
                let dinheiro = $('#input-dinheiro').val();
                let troco = $('#input-troco').val();
                aba = window.open("http://localhost:8989/admin/venda-manual/venda/imprimir/"+venda_id+"/"+forma_pagamento_id+"/"+zona_id+"/"+endereco+"/"+dinheiro+"/"+troco)
            } else {
                aba = window.open("http://localhost:8989/admin/venda-manual/venda/imprimir/"+venda_id+"/"+forma_pagamento_id+"/"+zona_id+"/"+endereco)
            }
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

            let total = parseFloat($("#input-total").val())

            document.getElementById("div_dinheiro").style.display = "none";

            $.fn.select2.defaults.set("theme", "classic");
            $(".js-example-basic-single").select2({
                placeholder: 'Selecione um produto',
                allowClear: true
            });

            $("#zona_id").on("change", function(){
                let id = $(this).val()
                let zona = "zona"+id
                let valorTemp = parseFloat($("#"+zona).val())
                let totalTemp = total
                let novoTotal = totalTemp + valorTemp
                $("#input-total").val(novoTotal.toFixed(2))
            })

            $("#input-dinheiro").blur(function(){
                let valor = parseFloat($(this).val())
                let total = parseFloat($("#input-total").val())
                if(valor < total){
                    $("#input-dinheiro").val("")
                    $("#input-troco").val("")
                } else {
                    calcular();
                }
            })

            $('.alert').addClass("show");
            $('.alert').removeClass("hide");
            $('.alert').addClass("showAlert");
            setTimeout(function(){
                $('.alert').removeClass("show");
                $('.alert').addClass("hide");
            },3500)
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
            var n1 = parseFloat(document.getElementById('input-dinheiro').value, 4, 2);
            var n2 = parseFloat(document.getElementById('input-total').value, 4);
            document.getElementById('input-troco').value = (n1 - n2).toFixed(2);
        }

        function aparecer(valor, div){
            if(valor == 1){
                let zona = $("#zona_id").val()
                if(zona == ""){
                    return toastr.error("Informe a ZONA primeiro", "Erro")
                }
                document.getElementById("div_dinheiro").style.display = "flex";
            } else {
                document.getElementById("div_dinheiro").style.display = "none";
            }
        }

    </script>
@stop