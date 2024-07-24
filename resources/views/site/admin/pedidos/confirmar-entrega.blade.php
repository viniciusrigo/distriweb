<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- Base Meta Tags --}}
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="_token" content="{{ csrf_token() }}">
        @if (auth()->user())
            <meta name="user" content="{{ auth()->user()->id }}">
        @endif

        <link rel="icon" href="{{ asset('dw.png') }}">

        {{-- Title --}}
        <title>Padovani Bebidas - Loja</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');
            @import url('https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');
            body{
                font-family: 'Poppins', sans-serif;
                background: #ececec;
            }
            .a-link {
                color: #888888;
            }
            .a-link:hover {
                color: black;
            }
            .scroll::-webkit-scrollbar{
                height: 7px;
            }
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
    </head>

    <body id="body" class="container-fluid">
        <div style="height: 100vh;" class="d-flex flex-wrap justify-content-center align-items-center">
            <div class="d-flex flex-wrap justify-content-center align-items-center">
                <div class="d-flex justify-content-center col-md-12 p-3">
                    <img class="rounded" src="{{ asset("logo.png") }}" alt="Padovani Bebidas Logo" width="200px" height="200px">
                </div>
                <div style="font-family: 'Ubuntu', sans-serif; color: #888888;" class="d-flex flex-wrap justify-content-center col-md-10 p-3">
                    <form id="confirma-entrega" class="d-flex flex-wrap" action="{{ route('confirmar-entrega') }}" method="GET">
                        <input class="form-control col-md-4 col-12 m-1" type="text" name="codigo_interno" placeholder="Código Interno" required>
                        <input class="form-control col-md-3 col-12 m-1" type="text" name="pedido_id" placeholder="Nº Pedido" required>
                        <input class="form-control col-md-4 col-12 m-1" type="text" name="codigo" placeholder="Código Pedido" required>
                    </form>
                    <button form="confirma-entrega" type="submit" class="btn btn-danger rounded-pill m-1">Confirmar</button>
                </div>
                @if (session('error')) 
                    <div class="alert-danger rounded p-2">
                        <strong>Erro!</strong> {{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert-success rounded p-2">
                        <strong>Sucesso!</strong> {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>
        
        <script>
            $(document).ready(function(){
                
            })
        </script>

        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    </body>

</html>

