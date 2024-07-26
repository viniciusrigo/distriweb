@extends('adminlte::page')

@section('title', 'DW - Fidelidade')

@section('content_header')

@stop

@section('css')
    <style>

    </style>
@stop

@section('content')
    <div class="d-flex justify-content-center row mb-1">
        
        {{-- TABELA --}}
        <div class="col-7">
            <div class="card table-responsive p-0">        
                <div class="card-body p-2">    
                    <div class="row">
                        <div class="col-12">
                            <table id="ativados" class="table hover compact">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th style="text-align: left">Pontos</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($ativados)
                                        @foreach ($ativados as $produto)
                                                <td>{{ $produto->nome }} {{ $produto->variavel_nome }}</td>
                                                <td style="text-align: left">{{ $produto->pontos }}</td>
                                                <td>
                                                    <form style="display: inline; padding: 0px;" action="{{ route('admin.fidelidade.remover') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="variavel_produto_id" value="{{ $produto->id }}">
                                                        <button style="border: none;" type="submit" class="badge badge-danger">Remover</button>
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
        {{-- FIM TABELA --}}

        {{-- TABELA --}}
        <div class="col-7">
            <div class="card table-responsive p-0">        
                <div class="card-body p-2">    
                    <div class="row">
                        <div class="col-12">
                            <table id="desativados" class="table hover compact">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th style="text-align: left">Pontos</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($desativados)
                                        @foreach ($desativados as $produto)
                                                <td>{{ $produto->nome }} {{ $produto->variavel_nome }}</td>
                                                <td style="text-align: left">{{ $produto->pontos }}</td>
                                                <td>
                                                    <form action="{{ route('admin.fidelidade.adicionar') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="variavel_produto_id" value="{{ $produto->id }}">
                                                        <input style="width: 50px; height: 25px; border-radius: 3px; border: 3px solid #e0e0e0" type="text" name="pontos" maxlength="4">
                                                        <button type="submit" style="border: none;" class="badge badge-success">Adicionar</button>
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

            new DataTable('#ativados', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                pagingType: 'first_last_numbers',
                order: [[0, 'asc']]
            });
            new DataTable('#desativados', {
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                },
                paging: false,
                scrollCollapse: true,
                scrollY: '57vh',
                order: [[0, 'asc']]
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