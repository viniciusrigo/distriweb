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

        <div id="div_manifesto" class="col-12">
            <div class="row p-2 justify-content-center">
                <div style="border-radius:6px" class="d-flex col-12 col-md-12 bg-white justify-content-center m-1 p-2">
                    <form class="d-flex justify-content-center form-row col-10" action="{{ route('admin.fornecedores.novo') }}" method="POST">
                        @csrf
                        <div class="form-group col-md-3" style="padding: 3px;">
                            <label for="cnpj" style="margin: 0px;">CNPJ / CPF<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cnpj" name="cnpj" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" maxlength="14" required>
                        </div>
                        <div class="form-group col-md-2" style="padding: 3px;">
                            <label for="contato" style="margin: 0px;">Contato<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="contato" name="contato" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" maxlength="11" required>
                        </div>
                        <div class="form-group col-md-3" style="padding: 3px;">
                            <label for="nome" style="margin: 0px;">Nome<code>*</code></label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="nome" name="nome" maxlength="100">
                        </div>
                        <div class="form-group col-md-3" style="padding: 3px;">
                            <label for="fantasia" style="margin: 0px;">Fantasia</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="fantasia" name="fantasia" maxlength="50">
                        </div>
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <label for="cep" style="margin: 0px;">CEP</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="cep" name="cep" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13" maxlength="8">
                        </div>
                        <div class="form-group col-md-3" style="padding: 3px;">
                            <label for="logradouro" style="margin: 0px;">Logradouro</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="logradouro" name="logradouro" readonly>
                        </div>
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <label for="numero" style="margin: 0px;">Nº</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="numero" name="numero" maxlength="6">
                        </div>
                        <div class="form-group col-md-2" style="padding: 3px;">
                            <label for="municipio" style="margin: 0px;">Município</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="municipio" name="municipio" readonly>
                        </div>
                        <div class="form-group col-md-3" style="padding: 3px;">
                            <label for="bairro" style="margin: 0px;">Bairro</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="bairro" name="bairro" readonly>
                        </div>
                        <div class="form-group col-md-1" style="padding: 3px;">
                            <label for="uf" style="margin: 0px;">UF</label>
                            <input type="text" style="margin: 0px;" class="form-control form-control-border border-width-2" id="uf" name="uf" readonly>
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
                                        <th style="text-align: left;">CNPJ</th>
                                        <th style="text-align: left;">Contato</th>
                                        <th>Nome</th>
                                        <th>Fantasia</th>
                                        <th style="text-align: left;">CEP</th>
                                        <th style="text-align: left;">Nº</th>
                                        <th>Município</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($fornecedores)
                                        @foreach ($fornecedores as $fornecedor)
                                            <tr class="tb-tr-bd">                        
                                                <td style="text-align: left;">{{ $fornecedor->cnpj }}</td>
                                                <td style="text-align: left;">{{ $fornecedor->contato }}</td>
                                                <td>{{ $fornecedor->nome }}</td>
                                                <td>{{ $fornecedor->fantasia }}</td>
                                                <td style="text-align: left;">{{ $fornecedor->cep }}</td>
                                                <td style="text-align: left;">{{ $fornecedor->numero }}</td>
                                                <td>{{ $fornecedor->municipio }}</td>
                                                @php                                                
                                                    if ($fornecedor->status == "a") {
                                                        echo "<td><span style='background-color: #a4ee92; color: #255a1e; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Ativo</strong></span></td>";
                                                    } else {
                                                        echo "<td><span style='background-color: #ee9292; color: #DC3545; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Inativo</strong></span></td>";
                                                    }
                                                @endphp
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
                order: [[0, 'asc']]
            });

            $('#cep').on('blur', function() {
                var busca = this.value;
                $.get('https://viacep.com.br/ws/'+busca+'/json/', function (dados){
                    $('#logradouro').val(dados.logradouro)
                    $('#bairro').val(dados.bairro)
                    $('#municipio').val(dados.localidade)
                    $('#uf').val(dados.uf)
                })  
            })

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