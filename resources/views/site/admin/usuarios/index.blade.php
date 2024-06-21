@extends('adminlte::page')

@section('title', 'DW - Usuários')

@section('content_header')

@stop

@section('css')

    <style>
        ::-webkit-scrollbar{
            width: 7px;
        }
        ::-webkit-scrollbar-thumb{
            border-radius: 30px;
            background-color: #cccccc;
        }
        ::-webkit-scrollbar-thumb:hover{
            border-radius: 30px;
            background-color: #a6a6a6;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@stop

@section('content')
    <div class="d-flex justify-content-center row">
        <div class="col-12 mt-2">
            <div class="card table-responsive p-0">        
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="tabela_usuarios" class="table hover compact" aria-describedby="info">
                                <thead>
                                    <tr>
                                        <th rowspan="1" colspan="1">Nome</th>
                                        <th rowspan="1" colspan="1">E-mail</th>
                                        <th rowspan="1" colspan="1">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($users)
                                        @foreach ($users as $user)
                                            <tr class="tb-tr-bd">                      
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <form style="display: inline;" action="usuarios/editar/{{ $user->id }}" method="GET">
                                                        <button style="border: none;" class="badge badge-primary">Editar Permissões</button>
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

            new DataTable('#tabela_usuarios', {
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