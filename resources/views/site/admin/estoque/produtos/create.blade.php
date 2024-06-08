@extends('adminlte::page')

@section('title', 'DW - Novo Produto')

@section('content_header')
    
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@endsection

@section('content')
    @if (session('error'))
        <div style="background: #d47a7a; border-left: 8px solid #771c1c;" class="alert hide">
            <span style="color: #ffffff;" class="icon-alert fa-solid fa-circle-check"></span>
            <span style="color: #ffffff;" class="msg">{{ session('error') }}</span>
        </div>
    @endif
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Cadastrar Novo Produto</h3>
        </div>
        
        <div class="card-body">
            <form class="form-row" action="{{ route('admin.estoque.produtos.store') }}" method="POST">
                @csrf
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="nome" style="margin: 0px;">Nome Produto<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="nome" name="nome" autofocus required>
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="codigo_barras" style="margin: 0px;">Código de Barras<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="codigo_barras" name="codigo_barras"  required>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="preco" style="margin: 0px;">Preço<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco" name="preco" required>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="preco_custo" style="margin: 0px;">Preço Custo<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco_custo" name="preco_custo" required>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="preco_promocao" style="margin: 0px;">Preço Promoção</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="preco_promocao" name="preco_promocao">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="quantidade" style="margin: 0px;">QTD<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="quantidade" name="quantidade" required>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="sku" style="margin: 0px;">SKU</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="sku" name="sku">
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="categoria_id" style="margin: 0px;">Categoria<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="categoria_id" name="categoria_id" required>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="pontos" style="margin: 0px;">Pontos</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="pontos" name="pontos">
                </div>
                <div class="form-group col-md-12"><strong style="color: #007BFF;">Dados Fiscais</strong></div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="cfop" style="margin: 0px;">CFOP</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cfop" name="cfop">
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="ncm" style="margin: 0px;">NCM</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="ncm" name="ncm">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="cst_csosn" style="margin: 0px;">CSOSN</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_csosn" name="cst_csosn">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="cst_pis" style="margin: 0px;">PIS</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_pis" name="cst_pis">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="cst_cofins" style="margin: 0px;">COFINS</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_cofins" name="cst_cofins">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="cst_ipi" style="margin: 0px;">IPI</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_ipi" name="cst_ipi">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="perc_icms" style="margin: 0px;">ICMS</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_icms" name="perc_icms">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="perc_pis" style="margin: 0px;">PIS</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_pis" name="perc_pis">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="perc_cofins" style="margin: 0px;">COFINS</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_cofins" name="perc_cofins">
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="perc_ipi" style="margin: 0px;">IPI</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_ipi" name="perc_ipi">
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="ult_compra" style="margin: 0px;">Última Compras</label>
                    <input style="margin: 0px;" class="form-control form-control-border border-width-2" id="ult_compra" name="ult_compra" value="null" disabled>
                </div>
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="data_cadastro" style="margin: 0px;">Data Cadastro<code>*</code></label>
                    <input type="date" style="margin: 0px;" class="form-control form-control-border border-width-2" id="data_cadastro" name="data_cadastro">
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="promocao" style="margin: 0px;">Promoção</label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="promocao" name="promocao">
                        <option value="n">Não</option>
                        <option value="s">Sim</option>
                    </select>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="ativo" style="margin: 0px;">Ativo<code>*</code></label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="ativo" name="ativo">
                        <option value="s">Sim</option>
                        <option value="n">Não</option>
                    </select>
                </div>
                <div class="d-flex form-group col-md-12">
                    <button style="margin-left: auto;" class="btn btn-success" type="submit">Cadastrar</button>
                </div>    
            </form>
        </div>
        
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function(){
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
@endsection