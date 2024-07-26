@extends('adminlte::page')

@section('title', 'DW - Novo Produto')

@section('content_header')
    
@stop

@section('css')
    <style>
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
            border-radius: .375em .375em .375em .375em;
        }

        label:last-child span {
            border-radius: .375em.375em .375em .375em;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex row">
        <nav class="d-flex align-items-center col-md-6">
            <ol class="breadcrumb col-md-12 p-3 m-2">
                <li class="breadcrumb-item"><a href="{{ route("admin.estoque.produtos.page-index") }}">Produtos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Criar Produto</li>
            </ol>
        </nav>
    </div>
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Cadastrar Novo Produto</h3>
        </div>
        
        <div class="card-body">
            <div class="d-flex justify-content-center row alert-warning show p-2 rounded">
                <div class="d-flex justify-content-center col-md-12">
                    <span>Dica nome produto: <strong>Nome Produto: "Tipo" "Marca" "Produto" - Variavel Nome: "Variavel""Unidade Medida"</strong></span>
                </div>
                <div class="d-flex justify-content-center col-md-12">
                    <span>Ex: <strong>Cerveja Skol Lata 350ml</strong></span>
                </div>
            </div>
            <form class="form-row" action="{{ route('admin.estoque.produtos.store') }}" method="POST">
                @csrf
                <div class="form-group col-md-3" style="padding: 3px;">
                    <label for="nome" style="margin: 0px;">Nome Produto<code>*</code></label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="nome" name="nome" maxlength="40" autofocus required>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="sku" style="margin: 0px;">SKU</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="sku" name="sku" maxlength="25">
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="categoria_id" style="margin: 0px;">Categoria<code>*</code></label>
                    <select id="categoria_id" style="margin: 0px;" class="custom-select form-control-border border-width-2" id="categoria_id" name="categoria_id" required>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-none div-fardo form-group col-md-2" style="padding: 3px;">
                    <label for="variavel_produto_id" style="margin: 0px;">Produto</label>
                    <select style="margin: 0px;" class="custom-select form-control-border border-width-2" name="variavel_produto_id">
                        @foreach ($produtos as $produto)
                            <option value="{{ $produto->id }}">{{ $produto->nome }} {{ $produto->variavel_nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-12"><strong style="color: #007BFF;">Variáveis</strong></div>
                <div class="top col-md-12">
                    <div class="principal d-flex col-md-12">
                        <div class="form-group col-md-2" style="padding: 3px;">
                            <label for="variavel_nome" style="margin: 0px;">Variável Nome</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="variavel_nome[]" maxlength="50">
                        </div>
                        <div class="form-group col-md-2" style="padding: 3px;">
                            <label for="codigo_barras" style="margin: 0px;">Código de Barras</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="codigo_barras[]" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13">
                        </div>
                        <div class="div-qtd form-group col-md-1" style="padding: 3px;">
                            <label for="variavel_quantidade" style="margin: 0px;">QTD<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="variavel_quantidade[]" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" maxlength="4">
                        </div>
                        <div class="d-none div-fardo form-group col-md-1" style="padding: 3px;">
                            <label for="fardo_quantidade" style="margin: 0px;">QTD no Fardo</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="fardo_quantidade[]" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" maxlength="4">
                        </div>
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <label for="preco" style="margin: 0px;">Preço<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="preco[]" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                        </div>
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <label for="preco_custo" style="margin: 0px;">Preço Custo<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="preco_custo[]" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                        </div>
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <label for="preco_promocao" style="margin: 0px;">Preço Promoção</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="preco_promocao[]" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7">
                        </div>
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <label for="pontos" style="margin: 0px;">Pontos</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" name="pontos[]" maxlength="4">
                        </div>
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <label for="validade" style="margin: 0px;">Validade<code>*</code></label>
                            <input type="date" style="margin: 0px;" class="form-control form-control-border border-width-2" name="validade[]" required>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center col-md-12">
                    <button id="variavel" class="btn btn-primary" type="button">+ Variável</button>
                </div>
                
                

    
                <div class="form-group col-md-12"><strong style="color: #007BFF;">Dados Fiscais</strong></div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="cfop" style="margin: 0px;">CFOP</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cfop" name="cfop" disabled>
                </div>
                <div class="form-group col-md-2" style="padding: 3px;">
                    <label for="ncm" style="margin: 0px;">NCM</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="ncm" name="ncm" disabled>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="cst_csosn" style="margin: 0px;">CSOSN</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_csosn" name="cst_csosn" disabled>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="cst_pis" style="margin: 0px;">PIS</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_pis" name="cst_pis" disabled>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="cst_cofins" style="margin: 0px;">COFINS</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_cofins" name="cst_cofins" disabled>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="cst_ipi" style="margin: 0px;">IPI</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cst_ipi" name="cst_ipi" disabled>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="perc_icms" style="margin: 0px;">ICMS</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_icms" name="perc_icms" disabled>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="perc_pis" style="margin: 0px;">PIS</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_pis" name="perc_pis" disabled>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="perc_cofins" style="margin: 0px;">COFINS</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_cofins" name="perc_cofins" disabled>
                </div>
                <div class="form-group col-md-1" style="padding: 3px;">
                    <label for="perc_ipi" style="margin: 0px;">IPI</label>
                    <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="perc_ipi" name="perc_ipi" disabled>
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
        
        $("#variavel").on("click", function(){
            var div = $(".principal").clone().appendTo(".principal")
            div.removeClass("principal")
            $(".top").append(div)
        })

        $(document).ready(function(){
            var _token = $('meta[name="_token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': _token
                }
            });

            $("#categoria_id").on("change", function(){
                var id = $(this).val()
                if(id == 6){
                    $(".div-fardo").removeClass("d-none")
                    $(".div-qtd").addClass("d-none")
                } else {
                    $(".div-fardo").addClass("d-none")
                    $(".div-qtd").removeClass("d-none")
                }
            })

            $(".variaveis").addClass("d-none")
            $("#variavel").on("click", function(){
                $(".variaveis").addClass("d-flex")
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