@extends('adminlte::page')

@section('title', 'DW - Produtos')

@section('content_header')

@stop

@section('css')
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
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-none">
                <span class="info-box-icon bg-info"><i class="fas fa fa-info"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Quatidade Produtos</span>
                    <span class="info-box-number">{{ $qtd_produtos }}</span>
                </div>           
            </div>
        </div>
    </div>
    <div class="row">
        <div style="margin: auto;" class="col-sm-4 col-md-4">
            <div class="dt-buttons btn-group flex-wrap">
                <button class="btn btn-secondary buttons-copy buttons-html5" tabindex="0" aria-controls="example1" type="button">
                    <span>Copiar</span>
                </button>
                <button class="btn btn-secondary buttons-csv buttons-html5" tabindex="0" aria-controls="example1" type="button">
                    <span>CSV</span>
                </button>
                <button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="example1" type="button">
                    <span>PDF</span>
                </button>
                <button class="btn btn-secondary buttons-print" tabindex="0" aria-controls="example1" type="button">
                    <span>Print</span>
                </button>
            </div>
        </div>
        <div class="col-sm-4 col-md-4">
            <a class="btn btn-success" href="{{ route('admin.estoque.produtos.create') }}" autofocus><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<strong>Novo</strong></a>
        </div>
    </div>
    <div class="card table-responsive p-0">        
        <div class="card-body">
            <div>    
                <div class="row">
                    <div class="col-sm-12">
                        <table id="tabela_estoque" class="table compact">
                            <thead>
                                <tr>
                                    <th rowspan="1" colspan="1">Produto</th>
                                    <th rowspan="1" colspan="1">Preço</th>
                                    <th style="text-align:left" rowspan="1" colspan="1">Lucro</th>
                                    <th style="text-align:left" rowspan="1" colspan="1">Custo</th>
                                    <th style="text-align:left" rowspan="1" colspan="1">QTD</th>
                                    <th rowspan="1" colspan="1">Últ Compra</th>
                                    <th rowspan="1" colspan="1">Promoção</th>
                                    <th rowspan="1" colspan="1">Ativo</th>
                                    <th rowspan="1" colspan="1">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($lista_produtos)
                                    @foreach ($lista_produtos as $produto)
                                        <tr>
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
                                                <button style="padding: 0px; margin-top: 2px;" class="btn"><a href="{{ route('admin.estoque.produtos.edit', $produto->id) }}"><i class="bi bi-pencil-square"></i></a></button>
                                                <form style="display: inline; padding: 0px;" action="{{ route('admin.estoque.produtos.destroy', $produto->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button style="padding: 0px; color: red;" class="btn " type="submit"><i class="bi bi-trash3 mr-3"></i></button>
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

            new DataTable('#tabela_estoque', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                pagingType: 'first_last_numbers',
                order: [[0, 'desc']]
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