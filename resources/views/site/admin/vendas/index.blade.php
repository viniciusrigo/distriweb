@extends('adminlte::page')

@section('title', 'DW - Vendas')

@section('content_header')

@stop

@section('css')

    <style>

    </style>

    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@stop

@section('content')
    <div class="d-flex justify-content-center row mb-1">
        <div class="col-12 mt-2">
            <div class="card table-responsive p-0">
                <div class="card-body p-2">
                    <div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="tabela_vendas" class="table hover compact">
                                    <thead>
                                        <tr>
                                            <th style="text-align:left">#</th>
                                            <th style="text-align:left">Cliente</th>
                                            <th style="text-align:left">Local</th>
                                            <th style="text-align:left">Valor</th>
                                            <th>Itens</th>
                                            <th>Pagamento</th>
                                            <th>Data</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($vendas)
                                            @foreach ($vendas as $venda)
                                                <tr class="tb-tr-bd"> 
                                                    <td style="text-align:left">{{ $venda->id }}</td>                     
                                                    <td style="text-align:left">{{ $venda->cpf_cliente == null ? "Não informado" : $venda->cpf_cliente }}</td>
                                                    <td style="text-align:left"><span style='padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>{{ $venda->local }}</strong></span></td>
                                                    <td style="text-align:left">
                                                        R${{ $venda->valor }}
                                                        @php
                                                            if($venda->taxa > 0){
                                                                echo "<span class='badge badge-danger'>";
                                                                echo "<i class='fa-solid fa-arrow-trend-down'></i>  R$";
                                                                echo round(($venda->valor * 100) / (100 - $venda->taxa) - $venda->valor, 2);
                                                                echo "</span>";
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td><span style="cursor: pointer;background-color:#92d8ee;padding: 3px;border-radius: 5px;" class="text-primary" onclick="itens({{ $venda->id }})"><strong>Visualizar</strong></span></td>
                                                    <td><span style='padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>{{ $venda->pagamento_nome }}</strong></span></td>
                                                    <td>{{ date('H:i:s d/m/Y', strtotime($venda->data_venda)) }}</td>
                                                    <td>
                                                        <form style="display: inline;" action="vendas/detalhe/{{ $venda->id }}" method="GET">
                                                            <button style="border: none;" class="badge badge-primary">Detalhes</button>
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
        
        function itens(id){
            $.ajax({
                url: "vendas/consulta-produtos",
                method: 'post',
                data: {
                    'id': id
                },
                success: function(dados){
                    var html = "";
                    for ( var i = 0; i < dados.length; i++ ) {
                        html += "<tr>";
                        html += "<td>"+dados[i].codigo_barras+"</td>"
                        html += "<td>"+dados[i].nome+"</td>"
                        html += "<td>R$"+dados[i].preco+"</td>"
                        html += "</tr>"
                    }
                    Swal.fire({
                        width: 540,
                        html: `
                        <table class=d-flex justify-content-center">
                            <tbody id="table" class="table compact">
                                ${html}
                            </tbody>
                        </table>
                        `
                    });
                }
            });
        }

        $(document).ready(function() {

            var _token = $('meta[name="_token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': _token
                }
            });

            new DataTable('#tabela_vendas', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                pagingType: 'first_last_numbers',
                order: [[0, 'desc']],
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