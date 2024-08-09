@extends('adminlte::page')

@section('title', 'DW - Produtos')

@section('content_header')

@stop

@section('css')
    <style>

    </style>
@stop

@section('content')
    <div class="d-flex justify-content-center row mb-1">
        {{-- INDICADORES --}}
        <div class="d-flex justify-content-center row col-11">
            <div class="info-box shadow-none col-md-3 col-sm-6 col-12 m-1">
                <span class="info-box-icon bg-info"><i class="fas fa fa-info"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Produtos</span>
                    <span class="info-box-number">{{ count($produtos) }}</span>
                </div>           
            </div>
            <div class="info-box shadow-none col-md-3 col-sm-6 col-12 m-1">
                <span class="info-box-icon bg-warning"><i class="fa-solid fa-box-archive text-white"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Quatidade</span>
                    <span class="info-box-number">{{ count($produtos) > 0 ? $produtos->sum('variavel_quantidade') : "" }}</span>
                </div>           
            </div>
        </div>
        
        {{-- AÇOES --}}
        <div class="col-12">
            <div class="row p-2 justify-content-center">
                <div style="border-radius:6px" class="d-flex col-12 col-md-12 bg-white justify-content-center m-1 p-2">
                    <form action="{{ route('admin.estoque.produtos.page-create') }}" method="GET">
                        <button type="submit" class="btn btn-success rounded-pill mr-1" autofocus><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<strong>Novo Produto</strong></button>
                    </form>
                    <form action="{{ route('admin.estoque.produtos.page-create-combo') }}" method="GET">
                        <button type="submit" class="btn btn-info rounded-pill mr-1" autofocus><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<strong>Novo Combo</strong></button>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- TABELA --}}
        <div class="col-12">
            <div class="card table-responsive p-0">        
                <div class="card-body p-2">    
                    <div class="row">
                        <div class="col-12">
                            <table id="tabela_produtos" class="table hover compact">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th style="text-align:left">Quantidade</th>
                                        <th>Categoria</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($lista_produtos)
                                        @foreach ($lista_produtos as $produto)
                                            <tr class="tb-tr-bd">
                                                @if ($produto->ativo == 's')
                                                    <td class="dtr-control sorting_1" tabindex="0">{{ $produto->nome }}</td>
                                                @else
                                                    <td class="dtr-control sorting_1" tabindex="0"><s>{{ $produto->nome }}</s></td>
                                                @endif
                                                <td style="text-align:left">{{ $produto->quantidade }}</td>
                                                <td>{{ $produto->categoria_nome }}</td>
                                                <td>
                                                    <a href="{{ route('admin.estoque.produtos.page-edit', $produto->id) }}" class="badge badge-info">Editar</a>
                                                </td>
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
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>      
                </div>
            </div>
        </div>
        {{-- FIM TABELA --}}
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

            new DataTable('#tabela_produtos', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                order: [[0, 'asc']],
                paging: false,
                scrollCollapse: true,
                scrollY: '650px',
            });

            $('.alert').addClass("show");
            $('.alert').removeClass("hide");
            $('.alert').addClass("showAlert");
            setTimeout(function(){
                $('.alert').removeClass("show");
                $('.alert').addClass("hide");
            },3500)

        })

    </script>
@stop