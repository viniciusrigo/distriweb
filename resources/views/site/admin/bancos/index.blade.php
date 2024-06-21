@extends('adminlte::page')

@section('title', 'DW - Bancos')

@section('css')
    <style>

    </style>
@stop

@section('content')
    <div class="d-flex justify-content-center align-items-center row vh-100">   
        <div class="d-flex row col-12 justify-content-center">
            @foreach ($bancos as $banco)  
                <div style="box-shadow: 0px 5px 20px #888888;" class="small-box bg-success col-md-5 col-sm-4 col-10 m-2">
                    <div class="inner">
                        <h3>R${{ $banco->saldo }}</h3>
                        <p>{{ $banco->nome }}</p>
                    </div>
                    <div class="icon">
                        <i class="fa-solid fa-piggy-bank"></i>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop

@section('js')
    <script>

    </script>
@stop