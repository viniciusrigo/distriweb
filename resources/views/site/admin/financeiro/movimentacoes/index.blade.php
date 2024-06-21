@extends('adminlte::page')

@section('title', 'DW - Movimentações')

@section('content_header')

@stop

@section('css')
    <style>

    </style>
@stop

@section('content')
<div class="d-flex justify-content-center row mb-1">
    <div class="col-12 mt-2">
        <div class="card table-responsive p-0">
            <div class="card-body p-2">
                <div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="tabela" class="hover compact">
                                <thead>
                                    <tr>
                                        <th style="text-align:left" >ID</th>
                                        <th style="text-align:left">Origem</th>
                                        <th style="text-align:left" >Valor</th>
                                        <th style="text-align:left" >Lucro</th>
                                        <th >Pagamento</th>
                                        <th >Tipo</th>
                                        <th >Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($movs_fin_e as $mov)
                                        <tr class="tb-tr-bd">
                                            <td style="text-align:left">{{ $mov->id }}</td>
                                            <td style="text-align:left"><span style='padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>{{ $mov->local }}</strong></span></td>
                                            <td style="text-align:left">
                                                R${{ $mov->valor }}
                                                
                                            </td>
                                            <td style="text-align:left">{{ $mov->lucro == null ? null : 'R$'.$mov->lucro }}</td>
                                            <td><span style='padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>{{$mov->forma_pagamentos_id}}</strong></span></td>
                                            @php                                                
                                            if ($mov->tipo == "e") {
                                                    echo "<td><span style='background-color: #a4ee92; color: #255a1e; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Entrada</strong></span></td>";
                                                } else {
                                                    echo "<td><span style='background-color: #ee9292; color: #DC3545; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Saída</strong></span></td>";
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