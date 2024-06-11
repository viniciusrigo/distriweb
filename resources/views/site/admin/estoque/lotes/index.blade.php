@extends('adminlte::page')

@section('title', 'DW - Produtos')

@section('content_header')

@stop

@section('css')
    <style>
        td {
            text-align:left
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
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Criar Novo Lote</h3>
        </div>
        <div class="card-body">
            <form class="form-row" action="{{ route('admin.estoque.lote.novo') }}" method="POST">
                @csrf
                <input type="hidden" name="produtos_id" value="{{ session('produtos_id') }}">
                <div id="div_codigo" class="form-group col-sm-2" style="padding: 3px;">
                    <label for="codigo_barras" style="margin: 0px;">Código de Barras<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="codigo_barras" name="codigo_barras" value="{{ session('codigo_barras') }}">
                </div>
                @if (session('divs'))
                    {!! session('divs') !!}
                @endif
                <input type="hidden" name="data_cadastro" value="{{ now() }}">
                <div class="col-sm-2 d-flex align-items-center justify-content-center">
                    <div class="d-flex form-group col-sm-12 mb-0">
                        <button class="btn btn-success ml-auto mr-auto" type="submit">Cadastrar</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <div class="card table-responsive p-0">
        <div class="card-body">
            <div>
                <div class="row">
                    <div class="col-sm-12">
                        <table id="tabela_lotes" class="table compact">
                            <thead>
                                <tr>
                                    <th style="text-align:left" rowspan="1" colspan="1">Produto</th>
                                    <th style="text-align:left" rowspan="1" colspan="1">QTD</th>
                                    <th style="text-align:left" rowspan="1" colspan="1">Código de Barras</th>
                                    <th style="text-align:left" rowspan="1" colspan="1">Preço</th>
                                    <th style="text-align:left" rowspan="1" colspan="1">Custo</th>
                                    <th style="text-align:left" rowspan="1" colspan="1">P. Promoção</th>
                                    <th style="text-align:left" rowspan="1" colspan="1">Data Cadastro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($lotes)
                                    @foreach ($lotes as $lote)
                                        <tr>
                                            <td style="text-align:left">{{ $lote->nome }}</td>
                                            <td style="text-align:left">{{ $lote->quantidade }}</td>
                                            <td style="text-align:left">{{ $lote->codigo_barras }}</td>
                                            <td style="text-align:left">R${{ $lote->preco }}</td>
                                            <td style="text-align:left">R${{ $lote->preco_custo }}</td>
                                            <td style="text-align:left">R${{ $lote->preco_promocao }}</td>
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
                                </tr>
                            </tfoot>
                        </table>
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