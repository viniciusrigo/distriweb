@extends('adminlte::page')

@section('title', 'DW - PDV')

@section('content_header')

@stop

@section('css')
    <style>
        
    </style> 
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@stop

@section('content')
    @if (session('success'))
        <div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
            <span style="color: #ffffff;" class="fas fa-solid fa-circle-check"></span>
            <span style="color: #ffffff;" class="msg">{{ session('success') }}</span>
         </div>
    @endif
    <div class="d-flex justify-content-center">
        <div style="margin-top: 40vh;">
            <form class="row" action="{{ route('vendedor.pdv.create') }}" method="GET">
                <input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" type="text" class="col-md-6 form-control form-control-border border-width-2" name="cpf_cliente" value="" placeholder="CPF" autofocus>
                <input type="hidden" name="local_id" value="4">
                <input type="hidden" name="data_venda" value="@php echo now(); @endphp">
                <button type="submit" class="ml-auto mr-auto btn btn-success">Nova Venda</button>
            </form>
        </div>  
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function(){
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
@endsection