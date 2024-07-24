@extends('adminlte::page')

@section('title', 'DW - Vendas - Detalhe')

@section('content_header')

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-5 p-3 mt-5 bg-white">
                <div>Nº da Venda:.................... {{ $venda->id }}</div>
                <div>CPF Cliente:..................... {{ $venda->cpf_cliente }}</div>
                <div>Local Venda:.................... {{ $venda->local }}</div>
                <div>Valor Recebido:............... <span style="color: green">R${{ $venda->valor }}</span></div>
                <div>Pontos:............................ {{ $venda->pontos == null ? "0" : $venda->pontos }} pontos</div>
                <div>Nota Fiscal:..................... <span style="color: red">{{ $venda->estado == "a" ? "Não gerada" : "Gerada" }}</span></div>
                <div>Forma Pagamento:......... {{ $venda->forma_pagamento }}</div>
                <div>Data:................................ {{ date('H:i:s d/m/Y', strtotime($venda->data_venda)) }}</div>
            </div>
            <div style="overflow: auto; height: 224px" class="col-md-5 mt-5 bg-white">
                <table class="table-sm">
                    <tbody>
                    @foreach ($produtos as $produto)
                        <tr>
                            <td>{{ $produto['codigo_barras'] }}</td>
                            <td>{{ $produto['nome'] }} {{ $produto['variavel_nome'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>           
            </div>
            <div class="row col-md-10 mt-3 justify-content-center">
                <button class="btn btn-primary"><i class="fa-regular fa-file-code"></i>   XML Temporário</button>
                <button class="btn btn-warning text-white ml-3"><i class="fa-regular fa-paper-plane"></i>   Transmitir</button>      
            </div>
        </div>
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

            $('.alert').addClass("show");
            $('.alert').removeClass("hide");
            $('.alert').addClass("showAlert");
            setTimeout(function(){
                $('.alert').removeClass("show");
                $('.alert').addClass("hide");
            },3500)  
                  
        })
    </script>
@stop