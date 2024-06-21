@extends('adminlte::page')

@section('title', 'DW - Produtos')

@section('content_header')

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
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
        {{-- INDICADORES --}}
        <div class="d-flex justify-content-center row col-11">
            <div class="info-box shadow-none col-md-3 col-sm-6 col-12 m-1">
                <span class="info-box-icon bg-info"><i class="fas fa fa-info"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Produtos</span>
                    <span class="info-box-number">{{ count($qtd_produtos) }}</span>
                </div>           
            </div>
            <div class="info-box shadow-none col-md-3 col-sm-6 col-12 m-1">
                <span class="info-box-icon bg-warning"><i class="fa-solid fa-box-archive text-white"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Quatidade</span>
                    <span class="info-box-number">{{ $qtd_produtos->sum('quantidade') }}</span>
                </div>           
            </div>
        </div>
        
        {{-- AÇOES --}}
        <div class="col-12">
            <div class="row p-2 justify-content-center">
                <div style="border-radius:6px" class="d-flex col-12 col-md-12 bg-white justify-content-center m-1 p-2">
                    <form action="{{ route('admin.estoque.produtos.create') }}" method="GET">
                        <button type="submit" class="btn btn-success" autofocus><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<strong>Novo</strong></button>
                    </form>
                    {{-- <div class="dt-buttons btn-group flex-wrap ml-2">
                        <button class="btn btn-secondary buttons-csv buttons-html5" tabindex="0" aria-controls="example1" type="button">
                            <span>CSV</span>
                        </button>
                        <button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="example1" type="button">
                            <span>PDF</span>
                        </button>
                    </div> --}}
                </div>
            </div>
        </div>
        
        {{-- TABELA --}}
        <div class="col-12">
            <div class="card table-responsive p-0">        
                <div class="card-body p-2">    
                    <div class="row">
                        <div class="col-12">
                            <table id="tabela_estoque" class="table hover compact">
                                <thead>
                                    <tr>
                                        <th rowspan="1" colspan="1">Produto</th>
                                        <th rowspan="1" colspan="1">Preço</th>
                                        <th style="text-align:left" rowspan="1" colspan="1">Lucro</th>
                                        <th style="text-align:left" rowspan="1" colspan="1">Custo</th>
                                        <th style="text-align:left" rowspan="1" colspan="1">QTD</th>
                                        <th rowspan="1" colspan="1">Últ Compra</th>
                                        <th rowspan="1" colspan="1">Validade</th>
                                        <th rowspan="1" colspan="1">Promo</th>
                                        <th rowspan="1" colspan="1">Ativo</th>
                                        <th rowspan="1" colspan="1">#</th>
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
                                                <td>R${{ $produto->promocao == "s" ? $produto->preco_promocao : $produto->preco }} <span class="badge badge-success">{{ $produto->promocao == "s" ? number_format((($produto->preco_promocao - $produto->preco_custo) / $produto->preco_promocao) * 100, 2, '.', ',') : number_format((($produto->preco - $produto->preco_custo) / $produto->preco) * 100, 2, '.', ',') }}%</span> </td>
                                                <td  style="text-align:left">R${{ $produto->lucro }}</td>
                                                <td  style="text-align:left">R${{ $produto->preco_custo }}</td>
                                                <td  style="text-align:left">{{ $produto->quantidade }}</td>
                                                <td>
                                                    @php 
                                                        if($produto->ult_compra == null) {
                                                            echo "NULL";
                                                        } else {
                                                            echo date("d/m/Y", strtotime($produto->ult_compra));
                                                        } 
                                                    @endphp
                                                </td>
                                                <td>{{ date("d/m/Y", strtotime($produto->validade)) }}</td>
                                                <td>
                                                    @if ($produto->promocao == 's')
                                                        <form action="/admin/estoque/produtos/atualizar-promocao/" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status_promocao" value="ativado">
                                                            <input type="hidden" name="id_produto" value="{{ $produto->id }}">
                                                            <input onChange="this.form.submit()" type="checkbox" checked>
                                                        </form>
                                                    @else
                                                        <form action="/admin/estoque/produtos/atualizar-promocao/" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status_promocao" value="desativado">
                                                            <input type="hidden" name="id_produto" value="{{ $produto->id }}">
                                                            <input onChange="this.form.submit()" type="checkbox">
                                                        </form>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($produto->ativo == 's')
                                                    <form action="/admin/estoque/produtos/atualizar-ativo/" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status_ativo" value="ativado">
                                                        <input type="hidden" name="id_produto" value="{{ $produto->id }}">
                                                        <input onChange="this.form.submit()" type="checkbox" checked>
                                                    </form>
                                                @else
                                                    <form action="/admin/estoque/produtos/atualizar-ativo/" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status_ativo" value="desativado">
                                                        <input type="hidden" name="id_produto" value="{{ $produto->id }}">
                                                        <input onChange="this.form.submit()" type="checkbox">
                                                    </form>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.estoque.produtos.edit', $produto->id) }}" class="badge badge-info">Editar</a>
                                                    <form style="display: inline; padding: 0px;" action="{{ route('admin.estoque.produtos.destroy', $produto->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button style="border: none;" type="submit" class="badge badge-danger">Excluir</button>
                                                    </form>
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
        {{-- FIM TABELA --}}
    </div>
@stop

@section('js')
    <script>
        
        $(document).ready(function() {

            new DataTable('#tabela_estoque', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                pagingType: 'first_last_numbers',
                order: [[0, 'asc']]
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

            $('#deletar').click((e) => {
                e.preventDefault();
                $('#form-delete').submit()
            })
            $('.editar').click((e) => {
                e.preventDefault();
                $('#form-editar').submit()
            })

        })

    </script>
@stop