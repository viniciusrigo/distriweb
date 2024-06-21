<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- Base Meta Tags --}}
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="_token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('dw.png') }}">

        {{-- Title --}}
        <title>DW - Cliente Login</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');
            body{
                font-family: 'Poppins', sans-serif;
                background: #ececec;
            }
            @media (max-width: 576px){
                div #image {
                    display: none;
                }
            }
            @media (min-width: 577px){
                div #image {
                    display: flex;
                }
            }
        </style>

    </head>

    <body class="">
        <div class="conteiner d-flex justify-content-center align-items-center vh-100">
           <div class="row border d-flex rounded-5 p-3 bg-white m-3">
                <div id="image" class="col-md-6 rounded-4 justify-content-center align-items-center flex-column left-box" style="background: #103cbe;">
                    <div class="featured-image mb-3">
                        <img src="{{ asset('logincliente.png') }}" class="img-fluid" style="width: 250px;">
                    </div>
                    <p class="text-white fs-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Bora pedir?</p>
                    <small class="text-white text-wrap text-center" style="width: 17rem;font-family: 'Poppins', sans-serif;">Compre e receba sem sair de onde está !</small>
                </div>          
                <div class="col-md-6 right-box">
                    <div class="row align-items-center">
                        <div class="header-text mb-4">
                            <h2>Bem-Vindo(a)</h2>
                            <p>Que bom te ver novamente.</p>
                        </div>
                        <form action="{{ route('logincliente.auth') }}" method="post">
                            @csrf
                            <div class="input-group mb-3">
                                <input type="text" class="form-control form-control-lg bg-light fs-6" name="cpf" placeholder="Digite seu CPF">
                            </div>
                            <div class="input-group mb-1">
                                <input type="password" class="form-control form-control-lg bg-light fs-6" name="password" placeholder="Digite sua senha">
                            </div>
                            <div class="input-group mb-5 d-flex justify-content-between">
                                <div class="forgot">
                                    <small><a href="#">Esqueceu sua senha?</a></small>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <button class="btn btn-lg btn-primary w-100 fs-6">Acessar</button>
                            </div>
                        </form>
                        <div class="input-group mb-3">
                            <button class="btn btn-lg btn-light w-100 fs-6"><img src="{{ asset('google.png') }}" style="width:20px" class="me-2"><small>Entrar com o Google</small></button>
                        </div>
                        <div class="row">
                            <small>Não é cliente? <a href="{{ route('novo.cliente') }}">Cadastre-se</a></small>
                        </div>
                    </div>
                </div> 
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>   
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    </body>

</html>
