@extends('adminlte::page')

@section('title', 'DW - Lotes')

@section('content_header')

@stop

@section('css')

    <style>
        ::-webkit-scrollbar{
            width: 7px;
        }
        ::-webkit-scrollbar-thumb{
            border-radius: 30px;
            background-color: #cccccc;
        }
        ::-webkit-scrollbar-thumb:hover{
            border-radius: 30px;
            background-color: #a6a6a6;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@stop

@section('content')
    {{-- POP UPs --}}
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

    <div class="d-flex justify-content-center row mb-1">
        {{-- AÇÕES --}}
        <div class="col-12">
            <div class="row p-2 justify-content-center">
                <div style="border-radius:6px" class="d-flex col-12 bg-white justify-content-center m-1 p-2">
                    <form action="{{ route('admin.estoque.lote.novo') }}" method="POST">
                        @csrf
                        <input type="hidden" name="produtos_id" value="{{ session('produtos_id') }}">
                        <div id="div_codigo" class="row justify-content-center">
                            <div class="m-1">
                                <label for="codigo_barras" style="margin: 0px;">Código de Barras<code>*</code></label>
                                <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="codigo_barras" name="codigo_barras" value="{{ session('codigo_barras') }}">
                            </div>
                            @if (session('divs'))
                                {!! session('divs') !!}
                            @endif
                        </div>
                        <input type="hidden" name="data_cadastro" value="{{ now() }}">
                        <div class="d-flex m-1">
                            <button class="btn btn-success ml-auto mr-auto" type="submit">Cadastrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- TABELA --}}
        <div class="col-12">
            <div class="card table-responsive p-0">
                <div class="card-body p-2">
                    <div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="tabela_lotes" class="table hover compact">
                                    <thead>
                                        <tr>
                                            <th style="text-align:left" rowspan="1" colspan="1">Produto</th>
                                            <th style="text-align:left" rowspan="1" colspan="1">QTD</th>
                                            <th style="text-align:left" rowspan="1" colspan="1">Código de Barras</th>
                                            <th style="text-align:left" rowspan="1" colspan="1">Preço</th>
                                            <th style="text-align:left" rowspan="1" colspan="1">Custo</th>
                                            <th style="text-align:left" rowspan="1" colspan="1">Promoção</th>
                                            <th style="text-align:left" rowspan="1" colspan="1">Validade</th>
                                            <th style="text-align:left" rowspan="1" colspan="1">Data Cadastro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($lotes)
                                            @foreach ($lotes as $lote)
                                                <tr class="tb-tr-bd">
                                                    <td style="text-align:left">{{ $lote->nome }}</td>
                                                    <td style="text-align:left">{{ $lote->quantidade }}</td>
                                                    <td style="text-align:left">{{ $lote->codigo_barras }}</td>
                                                    <td style="text-align:left">R${{ $lote->preco }}</td>
                                                    <td style="text-align:left">R${{ $lote->preco_custo }}</td>
                                                    <td style="text-align:left">R${{ $lote->preco_promocao }}</td>
                                                    <td style="text-align:left">{{ date("d/m/Y", strtotime($lote->validade)) }}</td>
                                                    <td style="text-align:left">{{ date("H:i:s d/m/Y", strtotime($lote->data_cadastro)) }}</td>
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

            new DataTable('#tabela_lotes', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                pagingType: 'first_last_numbers'
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