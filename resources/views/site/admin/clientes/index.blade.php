@extends('adminlte::page')

@section('title', 'DW - Clientes')

@section('content_header')

@stop

@section('css')
    <style>

    </style> 
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@stop

@section('content')
    <div class="d-flex justify-content-center row">
        <div class="col-md-10">
            <div style="box-shadow: 0px 5px 20px #888888;" class="card table-responsive p-0">        
                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">    
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="tabela_clientes" class="table compact" aria-describedby="info">
                                    <thead>
                                        <tr>
                                            <th rowspan="1" colspan="1">Nome</th>
                                            <th rowspan="1" colspan="1">Celular</th>
                                            <th style="text-align:left" rowspan="1" colspan="1">Pontos</th>
                                            <th rowspan="1" colspan="1">Endereço</th>
                                            <th rowspan="1" colspan="1">Bairro</th>
                                            <th rowspan="1" colspan="1">Zona</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($clientes)
                                            @foreach ($clientes as $cliente)
                                                <tr>                      
                                                    <td>{{ $cliente['name'] }}</td>
                                                    <td>{{ $cliente['celular'] }}</td>
                                                    <td style="text-align:left">{{ $cliente['pontos'] }}</td>
                                                    <td>{{ $cliente['logradouro'] }}</td>
                                                    <td>{{ $cliente['bairro'] }}</td>
                                                    <td>{{ $cliente['zona'] }}</td>
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
        </div>
    </div>
@stop

@section('js')
    <script>
        

        $(document).ready(function(){
            new DataTable('#tabela_clientes', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                pagingType: 'first_last_numbers',
                order: [[0, 'asc']]
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