@extends('adminlte::page')

@section('title', 'DW - Editar Produto')

@section('content_header')
    
@stop

@section('css')
    <style>
        
    </style>
@stop

@section('content')
    <div class="d-flex row">
        <nav class="d-flex align-items-center col-md-6">
            <ol class="breadcrumb col-md-12 p-3 m-2">
                <li class="breadcrumb-item"><a href="{{ route("admin.estoque.produtos.page-index") }}">Produtos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar <strong>{{ $produto->nome }}</strong></li>
            </ol>
        </nav>
    </div>

    <div class="d-flex justify-content-center row">
        <div class="card card-warning col-md-12">
            <div class="card-header">
                <h3 style="color: white;" class="card-title">Editar</h3>
            </div>
            
            <div class="card-body">
                <form class="d-flex justify-content-center align-items-end form-row" action="{{ route('admin.estoque.produtos.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                    <div class="form-group col-md-2" style="padding: 3px;">
                        <label for="nome" style="margin: 0px;">Nome Produto<code>*</code></label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="nome" name="nome" value="{{ $produto->nome }}" maxlength="40" required>
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="sku" style="margin: 0px;">SKU</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="sku" name="sku" value="{{ $produto->sku }}" maxlength="25">
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="categoria_id" style="margin: 0px;">Categoria<code>*</code></label>
                        <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="categoria_id" name="categoria_id" {{ $produto->categoria_id == 6 || $produto->categoria_id == 5 ? "disabled" : "required" }}>
                            @foreach ($categorias as $categoria)
                                @if ($categoria->id == $produto->categoria_id)
                                    <option value="{{ $categoria->id }}" selected>{{ $categoria->nome }}</option>
                                @else
                                    <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                @endif
                                
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-center form-group col-md-12" style="padding: 3px;">
                        <button class="btn btn-success" type="submit">Atualizar</button>
                    </div>
                </form>
                <div class="d-flex justify-content-center col-md-12">
                    <strong style="color: #FFC107;">Variáveis</strong>
                </div>
                <div class="d-flex justify-content-center col-md-12">
                    <table id="tabela_estoque" class="table hover compact">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Código Barras</th>
                                @if ($produto->categoria_id == 6)
                                    <th>Qtd no Fardo</th>
                                @else
                                    <th>Quantidade</th>
                                @endif
                                <th>Lucro</th>
                                <th>Preço</th>
                                <th>Preço Promoção</th>
                                <th>Preço Custo</th>
                                <th>Validade</th>
                                <th>Última Compra</th>
                                <th>Promoção</th>
                                <th>Ativo</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach ($variaveis as $variavel)
                                    <tr class="tb-tr-bd">
                                        <td>{{ $variavel->variavel_nome }}</td>
                                        <td>{{ $variavel->codigo_barras }}</td>
                                        @if ($produto->categoria_id == 6)
                                            <td>{{ $variavel->fardo_quantidade }}</td>
                                        @else
                                            <td>{{ $variavel->variavel_quantidade }}</td>
                                        @endif
                                        <td>R${{ $variavel->lucro }}</td>
                                        <td>R${{ $variavel->preco }}</td>
                                        <td>{{ $variavel->preco_promocao == 0 ? "" : "R$".$variavel->preco_promocao }}</td>
                                        <td>R${{ $variavel->preco_custo }}</td>
                                        <td>{{ $variavel->validade != null ? date("d/m/Y", strtotime($variavel->validade)) : "" }}</td>
                                        <td>{{ $variavel->ult_compra != null ? date("d/m/Y", strtotime($variavel->ult_compra)) : "" }}</td>
                                        <td>
                                            @if ($variavel->promocao == 's')
                                                <form action="/admin/estoque/produtos/variavel-promocao/" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status_promocao" value="ativado">
                                                    <input type="hidden" name="variavel_id" value="{{ $variavel->id }}">
                                                    <input onChange="this.form.submit()" type="checkbox" checked>
                                                </form>
                                            @else
                                                <form action="/admin/estoque/produtos/variavel-promocao/" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status_promocao" value="desativado">
                                                    <input type="hidden" name="variavel_id" value="{{ $variavel->id }}">
                                                    <input onChange="this.form.submit()" type="checkbox">
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($variavel->variavel_ativo == 's')
                                                <form action="/admin/estoque/produtos/variavel-status/" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status_ativo" value="ativado">
                                                    <input type="hidden" name="variavel_id" value="{{ $variavel->id }}">
                                                    <input onChange="this.form.submit()" type="checkbox" checked>
                                                </form>
                                            @else
                                                <form action="/admin/estoque/produtos/variavel-status/" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status_ativo" value="desativado">
                                                    <input type="hidden" name="variavel_id" value="{{ $variavel->id }}">
                                                    <input onChange="this.form.submit()" type="checkbox">
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            <form style="display: inline; padding: 0px;" action="{{ route('admin.estoque.produtos.destroy', $variavel->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button style="border: none;" type="submit" class="badge badge-danger">Excluir</button>
                                            </form>
                                        </td>
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
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <form style="display: none;" id="form-variavel" class=" justify-content-center align-items-center form-row" action="{{ route('admin.estoque.produtos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="variavel_nome" style="margin: 0px;">Variável Nome</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="variavel_nome[]" maxlength="50">
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="codigo_barras" style="margin: 0px;">Cód. de Barras</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="codigo_barras[]">
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="variavel_quantidade" style="margin: 0px;">QTD<code>*</code></label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="variavel_quantidade[]" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" maxlength="4" required>
                    </div>
                    @if ($produto->categoria_id == 6)
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <label for="fardo_quantidade" style="margin: 0px;">Qtd no Fardo<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="fardo_quantidade[]" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" maxlength="4" required>
                        </div>
                    @endif
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="preco" style="margin: 0px;">Preço<code>*</code></label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="preco[]" placeholder="R$" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="preco_custo" style="margin: 0px;">Custo<code>*</code></label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="preco_custo[]" placeholder="R$" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="preco_promocao" style="margin: 0px;">Promoção</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="preco_promocao[]" placeholder="R$" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7">
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="pontos" style="margin: 0px;">Pontos</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="pontos[]" maxlength="4">
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="validade" style="margin: 0px;">Validade<code>*</code></label>
                        <input type="date" style="margin: 0px;" class="form-control form-control-border border-width-2" name="validade[]" required>
                    </div>
                    <div class="d-flex justify-content-center form-group col-md-12" style="padding: 3px;">
                        <button class="btn btn-success" type="submit">Salvar</button>
                    </div>
                </form>
                @if ($produto->categoria_id != 5)
                    <div class="d-flex justify-content-center form-group col-md-12" style="padding: 3px;">
                        <button id="nova-variavel" class="btn btn-primary" type="button">Nova Variável</button>
                    </div>
                @endif
                
                @foreach ($variaveis as $variavel)
                    <form class="d-flex justify-content-center align-items-center form-row" action="{{ route('admin.estoque.produtos.update-variavel') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method("PUT")
                        <input type="hidden" name="variavel_produto_id" value="{{ $variavel->id }}">
                        <input type="hidden" name="categoria_id" value="{{ $produto->categoria_id }}">
                        <input type="hidden" name="preco_custo" value="{{ $variavel->preco_custo }}">
                        <div class="form-group col-md-2" style="padding: 3px;">
                            <label style="margin: 0px;">Nome Variável<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="variavel_nome" value="{{ $variavel->variavel_nome }}" maxlength="50">
                        </div>
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <label style="margin: 0px;">Preço<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="preco" value="{{ $variavel->preco }}"  onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                        </div>
                        @if ($produto->categoria_id != 5)
                            <div class="form-group col-md-1" style="padding: 3px;">
                                <label style="margin: 0px;">Promoção<code>*</code></label>
                                <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="preco_promocao" value="{{ $variavel->preco_promocao }}" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                            </div>
                        @else
                            <div class="form-group col-md-1" style="padding: 3px;">
                                <label style="margin: 0px;">Custo<code>*</code></label>
                                <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="preco_custo" value="{{ $variavel->preco_custo }}" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                            </div>
                        @endif
                        <div class="form-group col-md-2">
                            <input type="file" class="form-control-file" name="path_image">
                        </div> 
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <button class="btn btn-success" type="submit">Atualizar</button>
                        </div>
                    </form>
                @endforeach
                @isset($combo_produtos)
                    <form class="d-flex justify-content-center align-items-center form-row" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                        @foreach ($combo_produtos as $cb)
                            <div class="d-flex justify-content-center align-items-center col-md-10">
                                <div class="form-group col-md-3" style="padding: 3px;">
                                    <label style="margin: 0px;">Produto<code>*</code></label>
                                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" name="variavel_produto_id[]" required>
                                        @foreach ($produtos as $p)
                                            @if ($cb["id"] == $p["id"])
                                                <option value="{{ $p["id"] }}" selected>{{ $p["nome"] }} {{ $p["variavel_nome"] }}</option>
                                            @else
                                                <option value="{{ $p["id"] }}">{{ $p["nome"] }} {{ $p["variavel_nome"] }}</option>
                                            @endif
                                            
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-1" style="padding: 3px;">
                                    <label style="margin: 0px;">Quantidade<code>*</code></label>
                                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="combo_quantidade[]" value="{{ $cb["combo_quantidade"] }}"  onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" maxlength="4" required>
                                </div>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-center align-items-center col-md-10">
                            <div class="form-group col-md-1" style="padding: 3px;">
                                <button class="btn btn-success" type="submit">Atualizar</button>
                            </div>
                        </div>
                    </form>
                @endisset
                <div class="d-flex justify-content-center form-group col-md-12">
                    <strong style="color: #FFC107;">Dados Fiscais</strong>
                </div>
                <form class="d-flex justify-content-center align-items-center form-row" action="{{ route('admin.estoque.produtos.update') }}" method="POST" enctype="multipart/form-data">    
                    @csrf
                    @method("PUT")
                    <div class="form-group col-md-2" style="padding: 3px;">
                        <label for="cfop" style="margin: 0px;">CFOP</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cfop" name="cfop" value="{{ $produto->cfop }}" disabled>
                    </div>
                    <div class="form-group col-md-2" style="padding: 3px;">
                        <label for="ncm" style="margin: 0px;">NCM</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="ncm" name="ncm" value="{{ $produto->ncm }}" disabled>
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="cst_csosn" style="margin: 0px;">CSOSN</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_csosn" name="cst_csosn" value="{{ $produto->cst_csosn }}" disabled>
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="cst_pis" style="margin: 0px;">PIS</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_pis" name="cst_pis" value="{{ $produto->cst_pis }}" disabled>
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="cst_cofins" style="margin: 0px;">COFINS</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_cofins" name="cst_cofins" value="{{ $produto->cst_cofins }}" disabled>
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="cst_ipi" style="margin: 0px;">IPI</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_ipi" name="cst_ipi" value="{{ $produto->cst_ipi }}" disabled>
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="perc_icms" style="margin: 0px;">% ICMS</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_icms" name="perc_icms" value="{{ $produto->perc_icms }}" disabled>
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="perc_pis" style="margin: 0px;">% PIS</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_pis" name="perc_pis" value="{{ $produto->perc_pis }}" disabled>
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="perc_cofins" style="margin: 0px;">% COFINS</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_cofins" name="perc_cofins" value="{{ $produto->perc_cofins }}" disabled>
                    </div>
                    <div class="form-group col-md-1" style="padding: 3px;">
                        <label for="perc_ipi" style="margin: 0px;">% IPI</label>
                        <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_ipi" name="perc_ipi" value="{{ $produto->perc_ipi }}" disabled>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @isset($lotes)
        <div class="d-flex justify-content-center row">
            <div class="card card-warning col-md-8">           
                <div class="card-body">
                    <div class="d-flex justify-content-center form-group col-md-12">
                        <strong style="color: #FFC107;">Lotes</strong>
                    </div>
                    <div class="d-flex justify-content-center col-md-12">
                        <table id="tabela_estoque" class="table hover compact">
                            <thead>
                                <tr>
                                    <th>Quantidade</th>
                                    <th>Lucro</th>
                                    <th>Preço</th>
                                    <th>Preço Promoção</th>
                                    <th>Preço Custo</th>
                                    <th>Validade</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @foreach ($lotes as $lote)
                                        <tr class="tb-tr-bd">
                                            <td>{{ $lote->quantidade }}</td>
                                            <td>R${{ $lote->preco - $lote->preco_custo }}</td>
                                            <td>R${{ $lote->preco }}</td>
                                            <td>R${{ $lote->preco_promocao }}</td>
                                            <td>R${{ $lote->preco_custo }}</td>
                                            <td>{{ date("d/m/Y", strtotime($lote->validade)) }}</td>
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
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endisset
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

            $("#nova-variavel").on("click", function(){
                $("#form-variavel").addClass("d-flex")
                $("#nova-variavel").removeClass("d-flex")
                $("#nova-variavel").addClass("d-none")
            })

            $('.alert').addClass("show");
            $('.alert').removeClass("hide");
            $('.alert').addClass("showAlert");
            setTimeout(function(){
                $('.alert').removeClass("show");
                $('.alert').addClass("hide");
            },3500)
        })
    </script>
@endsection