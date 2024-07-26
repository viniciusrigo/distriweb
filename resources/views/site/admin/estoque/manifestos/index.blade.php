@extends('adminlte::page')

@section('title', 'DW - Produtos')

@section('content_header')

@stop

@section('css')
    <style>
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
    <div class="d-flex justify-content-center row mb-1">

        <div id="div_manifesto" class="col-12">
            <div class="row p-2 justify-content-center">
                <div style="border-radius:6px" class="d-flex col-12 col-md-12 bg-white justify-content-center m-1 p-2">
                    <form class="d-flex justify-content-center form-row col-10" action="{{ route('admin.estoque.manifesto.novo') }}" method="POST">
                        @csrf
                        <div class="d-flex justify-content-center  form-group col-md-12" style="padding: 3px;">
                            <div class="escolha">
                                <div>
                                    <label>
                                        <input type="radio" name="acao" value="Remover" checked="">
                                        <span>Remover</span>
                                    </label>
                                    <label>
                                        <input type="radio" name="acao" value="Adicionar">
                                        <span>Adicionar</span>
                                    </label>                                  
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-2" style="padding: 3px;">
                            <label for="variavel_produto_id" style="margin: 0px;">Produto<code>*</code></label>
                            <select name="variavel_produto_id" class="form-control form-control-md col-md-12" required>
                                @foreach ($produtos as $produto)
                                    <option value="{{ $produto->id }}">{{ $produto->produto_nome }} {{ $produto->variavel_nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <label for="quantidade" style="margin: 0px;">QTD<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="quantidade" name="quantidade" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="4" required>
                        </div>
                        <div class="form-group col-md-3" style="padding: 3px;">
                            <label for="observacao" style="margin: 0px;">Observação<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="observacao" name="observacao" maxlength="100" required>
                        </div>
                        <div class="d-flex justify-content-center form-group col-12" style="padding: 3px;">
                            <button type="submit" class="btn btn-success" autofocus></i>&nbsp;&nbsp;&nbsp;<strong>Salvar</strong></button>
                        </div>
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
                            <table id="tabela" class="table hover compact">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">#</th>
                                        <th>Ação</th>
                                        <th style="text-align: left;">Produto</th>
                                        <th style="text-align: left;">QTD</th>
                                        <th>Observação</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($manifestos)
                                        @foreach ($manifestos as $manifesto)
                                        <tr class="tb-tr-bd">                        
                                                <td style="text-align: left;">{{ $manifesto->id }}</td>
                                                <td>{{ $manifesto->acao }}</td>
                                                <td style="text-align: left;">{{ $manifesto->nome }} {{ $manifesto->variavel_nome }}</td>
                                                <td style="text-align: left;">{{ $manifesto->quantidade }}</td>
                                                <td>{{ $manifesto->observacao }}</td>
                                                <td>{{ date("H:i:s d/m/Y", strtotime($manifesto->data)) }}</td>
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

            new DataTable('#tabela', {
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
            },3500)

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