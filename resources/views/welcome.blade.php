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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');
            @import url('https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');
            body{
                font-family: 'Poppins', sans-serif;
                background: #ececec;
            }
            .scroll::-webkit-scrollbar{
                height: 9px;
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

    <body class="container-fluid">
        <div class="row justify-content-center">
            <div style="box-shadow: 0px 5px 20px #888888; border-radius: 10px;" class="row col-md-9 justify-content-center mt-2">
                <div class="col-md-1 mr-auto ml-auto">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('welcome') }}">
                                DistriWEB
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="d-flex col-md-3 justify-content-center align-items-center mr-auto ml-auto">
                    <div class="d-flex row justify-content-center">
                        <div class="d-flex justify-content-center align-items-center">
                            <a class="mr-1 ml-1 pl-3 pr-3" href=""><i class="fa-solid fa-cart-shopping"></i></a>
                            <a class="mr-1 ml-1 pl-3 pr-3" href="{{ route('logincliente') }}"><span class="">Fazer Login</span></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>



        <div class="row justify-content-center">
            <div style="border-radius: 10px;" class="row col-12 col-md-8 justify-content-center mt-2 bg-white">
                {{-- <div class="d-flex col-10 p-2">
                    <button style="background-color: #f7f7f7; border-radius: 50px; font-size:13px; border: none;" class="d-flex row justify-content-center align-items-centero col-2 m-3">
                        <div>
                            <span>Bebidas</span>
                        </div>
                        <div>
                            <img src="{{ asset('bebidas.png') }}" width="50px" height="50px">
                        </div>
                    </button>
                    <button style="background-color: #f7f7f7; border-radius: 50px; font-size:13px; border: none;" class="d-flex row justify-content-center align-items-centero col-2 m-3">
                        <div>
                            <span>Bebidas Alcoolicas</span>
                        </div>
                        <div>
                            <img src="{{ asset('bebidas-alcoolicas.png') }}" width="50px" height="50px">
                        </div>
                    </button>
                    <button style="background-color: #f7f7f7; border-radius: 50px; font-size:13px; border: none;" class="d-flex row justify-content-center align-items-centero col-2 m-3">
                        <div>
                            <span>Churrasco</span>
                        </div>
                        <div>
                            <img src="{{ asset('churrasco.png') }}" width="50px" height="50px">
                        </div>
                    </button>
                </div> --}}
                <div style="font-size: 1.7rem; font-family: 'Ubuntu', sans-serif;" class="d-flex justify-content-center col-10">
                    <span style="border-bottom: 5px solid #3e5284">Vamos de Promoção !?</span>
                </div>
                <div style="overflow-x: auto" class="scroll d-flex col-10 p-2">
                    @foreach ($produtos as $produto)
                        @if ($produto->promocao == "s")
                            <div class="card d-flex m-1 p-1 justify-content-center" style="width: 12rem; min-width: 12rem; background-color:#f7f7f7; border: none;">
                                <div class="d-flex justify-content-center">
                                    <div style="position: absolute;" class="d-flex col-12">
                                        <span class="badge badge-danger ml-auto">{{ abs(round((($produto->preco_promocao * 100) / $produto->preco) - 100, 2)) }}%</span>
                                    </div>
                                    <img src="{{ url('storage/produtos/'.$produto->codigo_barras.'.png') }}" alt="Card image cap" width="130px" height="130px">
                                </div>
                                <div class="card-body m-0 p-1">
                                    <div style="height: 40px" class="d-flex justify-content-center align-items-center">
                                        <h6 style="font-size: 14px;" class="card-title m-0">{{ $produto->nome }}</h6>
                                    </div>
                                    <div style="font-size: 16px;" class="d-flex justify-content-center align-itens-center">
                                        <span class="mr-3" onclick="remover({{ $produto->id }})" style="color: red; font-size: 25px; cursor: pointer;">-</span>
                                        <input id="{{ $produto->id }}" style="border:none; outline: 0; width:30px; background-color:#f7f7f7;" type="text" name="quantidade" value="1">
                                        <span class="ml-3" onclick="adicionar({{ $produto->id }})" style="color: red; font-size: 25px; cursor: pointer;">+</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center mb-0">
                                        <span style="font-size: 19px; color:#f65454;"><strong>R${{ $produto->promocao == "s" ? $produto->preco_promocao : $produto->preco }}</strong></span><a style="padding: 1px 15px 1px 15px;border-radius: 20px" onclick="addCarrinho()" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                @foreach ($produtos as $produto)
                    @if ($produto->promocao != "s")
                        <div class="card d-flex m-1 p-0 justify-content-center" style="width: 12rem; border: none; background-color: #f7f7f7;">
                            <div class="d-flex justify-content-center">
                                <img src="{{ url('storage/produtos/'.$produto->codigo_barras.'.png') }}" alt="Card image cap" width="130px" height="130px">
                            </div>
                            <div class="card-body m-0 p-1">
                                <div style="height: 40px" class="d-flex justify-content-center align-items-center">
                                    <h6 style="font-size: 14px;" class="card-title m-0">{{ $produto->nome }}</h6>
                                </div>
                                <div style="font-size: 16px;" class="d-flex justify-content-center align-itens-center">
                                    <span class="mr-3" onclick="remover({{ $produto->id }})" style="color: red; font-size: 25px; cursor: pointer;">-</span>
                                    <input id="{{ $produto->id }}" style="border:none; outline: 0; width:30px;background-color: #f7f7f7;" type="text" name="quantidade" value="1">
                                    <span class="ml-3" onclick="adicionar({{ $produto->id }})" style="color: red; font-size: 25px; cursor: pointer;">+</span>
                                </div>
                                <div class="d-flex justify-content-center align-items-center mb-0">
                                    <span class="text-primary" style="font-size: 19px;"><strong>R${{ $produto->promocao == "s" ? $produto->preco_promocao : $produto->preco }}</strong></span><a style="padding: 1px 15px 1px 15px;border-radius: 20px" onclick="addCarrinho()" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <script>
            function adicionar(id){
                var add = parseFloat($('#'+id).val()) + 1
                $('#'+id).val(add)
            }

            function remover(id){
                var add = parseFloat($('#'+id).val())
                if (add <= 1){
                    return $('#'+id).val(1)
                }
                var add = add - 1
                $('#'+id).val(add)
            }
            function addCarrinho(){
                window.location.href = "/login-cliente";
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    </body>

</html>

