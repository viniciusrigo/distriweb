@extends('adminlte::page')

@section('title', 'DW - Estoque')

@section('content_header')

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
@stop

@section('content')
    @if (session('success'))
        <div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
            <span style="color: #ffffff;" class="icon-alert fa-solid fa-circle-check"></span>
            <span style="color: #ffffff;" class="msg">{{ session('success') }}</span>
        </div>
    @endif
    <div class="d-flex row justify-content-center">
        <div class="row col-md-4 justify-content-center">
            <span>{{ $user->name }}</span>&nbsp;&nbsp;|&nbsp;&nbsp;<span>{{ $user->email }}</span>
            <div class="col-md-4">
                <form class="form-check" action="{{ route('admin.usuarios.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="usuario" value="{{ $user->id }}">
                    @php
                        for ($i=0; $i < count($permissions); $i++) { 
                            if(in_array($permissions[$i]["id"], $acessos)) {
                                echo '<input class="form-check-input" type="checkbox" value="'.$permissions[$i]["id"].'" name="'.$permissions[$i]["nome"].'" id="'.$permissions[$i]["id"].'" checked>';
                                echo '<label class="form-check-label" for="'.$permissions[$i]["id"].'">'.$permissions[$i]["nome"].'</label>';
                                echo '<br>';
                            } else {
                                echo '<input class="form-check-input" type="checkbox" value="'.$permissions[$i]["id"].'" name="'.$permissions[$i]["nome"].'" id="'.$permissions[$i]["id"].'">';
                                echo '<label class="form-check-label" for="'.$permissions[$i]["id"].'">'.$permissions[$i]["nome"].'</label>';
                                echo '<br>';
                            }
                        }
                    @endphp
                    <button class="btn btn-outline-success" type="submit">Salvar</button>
                </form>
                
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        
        $(document).ready(function() {

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