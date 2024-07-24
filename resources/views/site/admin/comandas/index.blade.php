@extends('adminlte::page')

@section('title', 'DW - Comandas')

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
                    <div class="card card-outline card-success col-md-3 mr-1">
                        <div class="card-header">
                            <h3 class="card-title"><strong>{{ $comanda->nome }}</strong></h3>
                            <div class="card-tools">
                                @if (count($comanda->produtos) == 0)
                                    <form style="display: inline; margin-left: auto;" action="comandas/delete/{{ $comanda->id }}" method="GET">
                                        @csrf
                                        <input type="hidden" name="comanda_id" value="{{ $comanda->id }}">
                                        <button style="border: none;" class="badge badge-danger" id="excluir_comanda">Excluir</button>
                                    </form>
                                @endif                               
                            </div>
                        </div>   
                        <div class="card-body p-0" style="display: block;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">      
                                        <div class="card-body table-responsive p-0" style="height: 200px;">
                                            <table style="cursor: default;" class="table-sm text-nowrap col-12">                                  
                                                <tbody>
                                                    @foreach ($comanda->produtos as $produtos)
                                                        <tr>
                                                            <td>{{ $produtos->produto_nome }} {{ $produtos->variavel_nome }}</td>
                                                            <td>R${{ $produtos->promocao == "s" ? $produtos->preco_promocao : $produtos->preco }}</td>
                                                            <td>
                                                                <form style="display: inline;" action="{{ route('admin.comandas.remove-produto') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="variavel_produto_id" value="{{ $produtos->id }}">
                                                                    <input type="hidden" name="comanda_id" value="{{ $comanda->id }}">
                                                                    <input type="hidden" name="data" value="{{ $produtos->data }}">
                                                                    <button type="submit" style="border: none;" class="badge badge-danger"><i class="fa fa-times-circle" aria-hidden="true"></i></button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach    
                                                </tbody>
                                            </table>
                                        </div>  
                                    </div>                    
                                </div>
                            </div>
                        </div>
                        <div class="card-footer row p-0">
                            <form class="ml-auto mr-auto col-md-9" action="{{ route('admin.comandas.add-produto') }}" method="POST">
                                @csrf
                                <input id="comanda_id" type="hidden" name="comanda_id" value="{{ $comanda->id }}">
                                <input type="text" style="margin: 0px;" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13"  class="form-control rounded-0 " id="codigo_barras" name="codigo_barras" placeholder="Código de barras">
                            </form>
                            <div class="col-12 mt-2">
                                <form action="{{ route('admin.comandas.add-produto') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="comanda_id" value="{{ $comanda->id }}">
                                    <div class="d-flex justify-content-center">
                                        <select name="variavel_produto_id" class="js-example-basic-single col-md-7">
                                            <option value=""></option>
                                            @foreach ($variaveis_produtos as $produto)
                                                <option value="{{ $produto->id }}">{{ $produto->produto_nome }} {{ $produto->variavel_nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="d-flex justify-content-center col-md-12 mt-2">
                                        <button style="margin: 0px;height: 2em;font-size: 20px;" type="submit" class="btn btn-success">Adicionar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-center row bg-white p-0">
                            {{-- Verifica se a comanda está aberta --}}
                            <div class="col-12">
                                <span class="badge badge-white"><i class="fa fa-circle text-success" aria-hidden="true"></i> <strong>Comanda Aberta</strong></span>
                            </div>
                            {{-- Verifica se a comanda tem produtos --}}
                            @if (!count($comanda->produtos) == 0)
                                <form id="fechar" class="d-flex flex-wrap col-12" style="display: inline; margin-left: auto;" action="{{ route('admin.comandas.closed') }}" method="POST">
                                    @csrf
                                    <div class="col-12 form-check p-2">
                                        <div class="escolha">
                                            <div>
                                                <label>
                                                    <input type="radio" name="pagamento" class="outros" onclick="aparecer(0)" id="pix" value="3" required>
                                                    <span>PIX</span>
                                                </label>
                                                <label>
                                                    <input type="radio" name="pagamento" class="dinheiro" onclick="aparecer(1)" id="dinheiro" value="2" required>
                                                    <span>Dinheiro</span>
                                                </label>                                  
                                                <label>
                                                    <input type="radio" name="pagamento" class="outros" onclick="aparecer(0)" id="credito" value="credito" required>
                                                    <span>Crédito</span>
                                                </label>                                  
                                                <label>
                                                    <input type="radio" name="pagamento" class="outros" onclick="aparecer(0)" id="debito" value="debito" required>
                                                    <span>Débito</span>
                                                </label>                     
                                            </div>
                                        </div>
                                    </div>
                                    <div class="justify-content-center col-12 p-0" id="div-dinheiro" style="padding: 3px;">
                                        <div class="col-5">
                                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="input-dinheiro" name="dinheiro" placeholder="R$0.00">
                                        </div>
                                        <div class="col-5">
                                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="input-troco" name="troco" placeholder="Troco" readonly>
                                        </div>
                                    </div>
                                    <input type="hidden" name="comanda_id" value="{{ $comanda->id }}">
                                    <input type="hidden" name="status" value="f">
                                </form>
                                <form id="imprimir" action="comandas/imprimir/{{ $comanda->id }}" method="GET" target="_blank"></form>
                                <div class="d-flex justify-content-center col-12 p-2">
                                    <button style="margin: 0px;height: 2em;font-size: 20px;" form="fechar" style="border: none;" class="badge badge-danger p-2">Fechar comanda</button>
                                    <button style="margin: 0px;height: 2em;font-size: 20px;" onclick="imprimir()" style="border: none;" class="badge badge-info p-2 ml-3"><i class="fa-solid fa-print"></i></button>
                                </div>   
                            @endif
                            <div style="font-size: 20px;" class="d-flex justify-content-center">
                                <span style="margin-left: 15px;">Total: <strong>R$</strong></span>
                                <input style="width: 70px; font-weight:bold; border: none; outline: none; color: black; background-color: white;" id="total" type="text" value="{{ $comanda->total }}" disabled>
                            </div>                   
                            <span style="margin-left: auto; margin-right: 15px; font-size: 20px;">Abertura: <strong>{{ $comanda->data_abertura }}</strong></span>
                        </div>  
                    </div>
                @endforeach
                {{-- @endisset --}}
            </div>
        </div>
        @can('acesso_vendas')
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
                                                <tr>
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
        @endcan
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function(){
            var _token = $('meta[name="_token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': _token
                }
            });

            $.fn.select2.defaults.set("theme", "classic");
            $(".js-example-basic-single").select2({
                placeholder: 'Selecione um produto',
                allowClear: true
            });

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
            },3500)
        })

        function imprimir(){
            let comanda = $('#comanda_id').val();
            let aba = window.open("comandas/imprimir/"+comanda)
            setTimeout(function(){
                aba.print()
            }, 0500);
            setTimeout(function(){
                aba.close()
            }, 3000);
        }

        function calcular() {
            var n1 = parseFloat(document.getElementById('input-dinheiro').value, 4, 2);
            var n2 = parseFloat(document.getElementById('total').value, 4);
            document.getElementById('input-troco').value = (n1 - n2).toFixed(2);
        }
        document.getElementById("input-dinheiro").onkeyup = function(){
            calcular();
        }

        document.getElementById("dinheiro").onclick = function(){
            document.getElementById("div-dinheiro").style.display = "flex";
            document.getElementById("div-troco").style.display = "flex";
        }
        document.getElementById("pix").onclick = function(){
            document.getElementById("div-dinheiro").style.display = "none";
            document.getElementById("div-troco").style.display = "none";
        }
        document.getElementById("credito").onclick = function(){
            document.getElementById("div-dinheiro").style.display = "none";
            document.getElementById("div-troco").style.display = "none";
        }
        document.getElementById("debito").onclick = function(){
            document.getElementById("div-dinheiro").style.display = "none";
            document.getElementById("div-troco").style.display = "none";
        }
    </script>
@stop