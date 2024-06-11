@extends('adminlte::page')

@section('title', 'DW - Estoque')

@section('content_header')

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@stop

@section('content')
    <div class="container-fluid">
        <div class="justify-content-center">
            <table id="tabela_vendas" class="table hover compact" aria-describedby="info">
                <thead>
                    <tr>
                        <th rowspan="1" colspan="1">Cliente</th>
                        <th rowspan="1" colspan="1">Local</th>
                        <th style="text-align:left" rowspan="1" colspan="1">Valor</th>
                        <th rowspan="1" colspan="1">Itens</th>
                        <th rowspan="1" colspan="1">Pagamento</th>
                        <th rowspan="1" colspan="1">Data</th>
                        <th rowspan="1" colspan="1">#</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($vendas)
                        @foreach ($vendas as $venda)
                            <tr>                      
                                <td>{{ $venda->cpf_cliente == null ? "Não informado" : $venda->cpf_cliente }}</td>
                                @php
                                    if ($venda->local == "PDV") {
                                        echo "<td><span style='background-color: #FFC107; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>PDV</strong></span></td>";
                                    } else{
                                        echo "<td><span style='background-color: #DC3545; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Comanda</strong></span></td>";
                                    }
                                @endphp
                                <td style="text-align:left">R${{ $venda->valor }}</td>
                                <td><span style="cursor: pointer;background-color:#92d8ee;padding: 3px;border-radius: 5px;" class="text-primary" onclick="itens({{ $venda->id }})"><strong>Visualizar</strong></span></td>
                                @php
                                    if ($venda->forma_pagamentos_id == 1) {
                                        echo "<td><span style='background-color: #514ab3; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-brands fa-pix'></i>  <strong>PIX</strong></span></td>";
                                    } else if ($venda->forma_pagamentos_id == 2) {
                                        echo "<td><span style='background-color: #949238; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-solid fa-credit-card'></i>  Crédito</span></td>";
                                    } else if ($venda->forma_pagamentos_id == 3) {
                                        echo "<td><span style='background-color: #ee9292; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-solid fa-credit-card'></i>  Débito</span></td>";
                                    } else {
                                        echo "<td><span style='background-color: #92d8ee; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-solid fa-money-bill-1'></i>  Dinheiro</span></td>";
                                    }
                                @endphp
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
                    </tr>
                </tfoot>
            </table>
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