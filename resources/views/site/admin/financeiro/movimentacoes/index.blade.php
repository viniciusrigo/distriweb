@extends('adminlte::page')

@section('title', 'DW - Movimentações')

@section('content_header')

@stop

@section('css')
    <style>
        .tb-tr-bd {
            -webkit-transition: -webkit-transform .05s ease;
            transition: transform .05s ease;
        }
        .tb-tr-bd:hover {
            -webkit-transform: scale(1.02);
            transform: scale(1.02);
        }
    </style>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <div class="card-header">
            <h3 class="card-title"><strong>Movimentações Financeiras</strong></h3>
        </div>
        <div class="card-body">
            <div>
                <div class="row">
                    <div class="col-sm-12">
                        <table id="tabela" class="hover compact">
                            <thead>
                                <tr>
                                    <th style="text-align:left" rowspan="1" colspan="1">ID</th>
                                    <th rowspan="1" colspan="1">Origem</th>
                                    <th style="text-align:left" rowspan="1" colspan="1">Valor</th>
                                    <th style="text-align:left" rowspan="1" colspan="1">Lucro</th>
                                    <th rowspan="1" colspan="1">Pagamento</th>
                                    <th rowspan="1" colspan="1">Tipo</th>
                                    <th rowspan="1" colspan="1">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($movs_fin_e as $mov)
                                    <tr class="tb-tr-bd">
                                        <td style="text-align:left"><strong>{{ $mov->id }}</strong></td>
                                        @php
                                            if ($mov->ponto_partida == "PDV") {
                                                echo "<td><span style='background-color: #FFC107; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>PDV</strong></span></td>";
                                            } else if ($mov->ponto_partida == "Comanda") {
                                                echo "<td><span style='background-color: #DC3545; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Comanda</strong></span></td>";
                                            } else {
                                                echo "<td><span style='background-color: #3554dc; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Contas a pagar</strong></span></td>";
                                            }
                                        @endphp
                                        <td style="text-align:left">
                                            <strong>R${{ $mov->valor }}
                                                    @php
                                                        if($mov->taxa > 0){
                                                            echo "<span class='badge badge-danger'>";
                                                            echo "<i class='fa-solid fa-arrow-trend-down'></i>  R$";
                                                            echo round(($mov->valor * 100) / (100 - $mov->taxa) - $mov->valor, 2);
                                                            echo "</span>";
                                                        }
                                                    @endphp
                                            </strong>
                                        </td>
                                        <td style="text-align:left"><strong>R${{ $mov->lucro }}</strong></td>
                                        @php
                                            if ($mov->forma_pagamentos_id == 1) {
                                                echo "<td><span style='background-color: #514ab3; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-brands fa-pix'></i>  <strong>PIX</strong></span></td>";
                                            } else if ($mov->forma_pagamentos_id == 2) {
                                                echo "<td><span style='background-color: #949238; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-solid fa-credit-card'></i>  Crédito</span></td>";
                                            } else if ($mov->forma_pagamentos_id == 3) {
                                                echo "<td><span style='background-color: #ee9292; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-solid fa-credit-card'></i>  Débito</span></td>";
                                            } else if ($mov->forma_pagamentos_id == 4) {
                                                echo "<td><span style='background-color: #92d8ee; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-solid fa-money-bill-1'></i>  Dinheiro</span></td>";
                                            } else {
                                                echo "<td><span style='background-color: #3d5f38; color: #ffffff; padding: 0px 8px 0px 8px; border-radius: 10px;'><i class='fa-solid fa-cash-register'></i>  Caixa Interno</span></td>";
                                            }
                                            
                                            if ($mov->tipo == "e") {
                                                echo "<td><span style='background-color: #a4ee92; color: #255a1e; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Entrada</strong></span></td>";
                                            } else {
                                                echo "<td><span style='background-color: #ee9292; color: #5e1b1b; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Saída</strong></span></td>";
                                            }
                                        @endphp
                                        <td>@php echo date("H:i d/m/y", strtotime($mov->data)) @endphp</td>
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
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            new DataTable('#tabela', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                pagingType: 'first_last_numbers',
                order: [[0, 'desc']],
            });
        })
    </script>
@stop