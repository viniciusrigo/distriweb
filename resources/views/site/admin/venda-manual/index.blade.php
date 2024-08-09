@extends('adminlte::page')

@section('title', 'DW - PDV')

@section('content_header')

@stop

@section('css')
    <style>
        
    </style> 
@stop

@section('content')
    <div class="d-flex justify-content-center">
        <div style="margin-top: 40vh;">
            <form class="row" action="{{ route('admin.venda-manual.create') }}" method="GET">
                <input type="hidden" name="data_venda" value="@php echo now(); @endphp">
                <button style="margin: 0px;height: 2em;font-size: 35px;" type="submit" class="ml-auto mr-auto btn btn-success" autofocus>Nova Venda</button>
            </form>
        </div>  
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function(){
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
@endsection