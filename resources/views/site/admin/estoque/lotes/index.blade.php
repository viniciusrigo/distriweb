@extends('adminlte::page')

@section('title', 'DW - Lotes')

@section('content_header')

@stop

@section('css')
    <style>

    </style>
@stop

@section('content')
    <div class="d-flex justify-content-center row mb-1">
        {{-- AÇÕES --}}
        <div class="col-12">
            <div class="row p-2 justify-content-center">
                <div style="border-radius:6px" class="d-flex col-12 bg-white justify-content-center m-1 p-2">
                    <form action="{{ route('admin.estoque.lote.novo') }}" method="POST">
                        @csrf
                        <input type="hidden" name="variavel_produto_id" value="{{ session('variavel_produto_id') }}">
                        <input type="hidden" name="produto_id" value="{{ session('produto_id') }}">
                        <div id="div_codigo" class="row justify-content-center">
                            <div class="d-flex align-items-center m-1">
                                <select name="variavel_produto_id" class="js-example-basic-single form-control form-control-border">
                                    @if (session('variavel_produto_id'))
                                        @for ($i = 0; $i < count($produtos); $i++)
                                            @if ($produtos[$i]->id == session('variavel_produto_id'))
                                                <option value="{{ $produtos[$i]->id }}">{{ $produtos[$i]->nome }} {{ $produtos[$i]->variavel_nome }}</option>
                                            @endif
                                        @endfor
                                    @else       
                                        @foreach ($produtos as $produto)
                                            <option value="{{ $produto->id }}">{{ $produto->nome }} {{ $produto->variavel_nome }}</option>
                                        @endforeach
                                    @endif
                                </select>
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
                                                    <td style="text-align:left">{{ $lote->nome }} {{ $lote->variavel_nome }}</td>
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
            },3500);

        })

    </script>
@stop