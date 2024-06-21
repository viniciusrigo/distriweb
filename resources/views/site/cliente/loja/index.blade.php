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
        <title>DW - Cliente Login</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');
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
        @livewireStyles
    </head>

    <body id="body" class="container-fluid">
        <div class="row justify-content-center">
            <div style="border-radius: 10px;" class="row col-12 col-md-8 justify-content-center mt-2">
                <div class="d-flex col-12 col-md-1 justify-content-center">
                    <a class="nav-link" href="{{ route('loja.index') }}">
                        DistriWEB
                    </a>
                </div>
                <div class="d-flex col-md-5 justify-content-center align-items-center mr-auto ml-auto">
                    <div class="d-flex row justify-content-center">
                        <div class="d-flex justify-content-center align-items-center">
                            <a href="#" class="a-link nav-link">Meus pedidos</a>
                            <a href="#" class="a-link nav-link">Meus Dados</a>
                        </div>
                    </div>
                </div>
                <div style="display: none;" class="col-md-3 justify-content-center align-items-center mr-auto ml-auto">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Opções
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">Minhas Compras</a>
                                <a class="dropdown-item" href="#">Meu dados</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Sair</a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="d-flex col-md-3 justify-content-center align-items-center mr-auto ml-auto">
                    <div class="d-flex row justify-content-center">
                        <div class="d-flex justify-content-center align-items-center">
                            <a class="a-link mr-1 ml-1 pl-3 pr-3" href="{{ route('loja.carrinho') }}">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span style="position:absolute;" id="carrinho" class="badge badge-info"></span>
                            </a>
                            <a style="text-decoration: none;" class="a-link mr-1 ml-1 pl-3 pr-3" href="{{route('logincliente.destroy')}}"><span>{{ auth()->user()->name }}</span></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="row justify-content-center">
            <div style="border-radius: 10px;" class="row col-12 col-md-8 justify-content-center mt-2 bg-white">
                <div style="font-size: 1.7rem; font-family: 'Ubuntu', sans-serif;" class="d-flex justify-content-center col-10">
                    <span style="border-bottom: 5px solid #3e5284">Vamos de Desconto !?</span>
                </div>
                <div style="overflow-x: auto" class="scroll d-flex col-10 p-2">
                    @foreach ($produtos_desconto as $produto)
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
                                        <span style="font-size: 19px; color:#f65454;"><strong>R${{ $produto->promocao == "s" ? $produto->preco_promocao : $produto->preco }}</strong></span><a style="padding: 1px 15px 1px 15px;border-radius: 20px" onclick="addCarrinho({{ auth()->user()->id }},{{ $produto->id }})" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="d-flex justify-content-end align-items-center col-10 p-2">
                    <form id="buscar" action="{{ route('loja.index') }}" method="GET"></form>
                    <a class="mr-4" href="{{ route('loja.index') }}">Ver Tudo</a>
                    <input form="buscar" name="busca" class="form-control col-4" type="text" placeholder="Buscar produto" value="{{ old('busca') }}">
                </div>
                <div id="geral" style="overflow: auto; height: 35rem" class="row justify-content-center">
                    @foreach ($produtos as $produto)
                        @if ($produto->promocao != "s")
                            <div class="card d-flex m-1 p-0 justify-content-center" style="width: 12rem; height: 250px; border: none; background-color: #f7f7f7;">
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
                                        <span class="text-primary" style="font-size: 19px;"><strong>R${{ $produto->promocao == "s" ? $produto->preco_promocao : $produto->preco }}</strong></span><a style="padding: 1px 15px 1px 15px;border-radius: 20px" onclick="addCarrinho({{ auth()->user()->id }},{{ $produto->id }})" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></a>
                                    </div>
                                </div>
                            </div>
                            @endif
                    @endforeach
                </div>
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

            function addCarrinho(user_id, produto_id){
                var qtd = $('#'+produto_id).val()
                var usuario = user_id
                var produto = produto_id

                $.ajax({
                    url: "/loja/add-carrinho",
                    method: 'post',
                    data: {
                        'usuario': usuario,
                        'produto': produto,
                        'qtd': qtd
                    },
                    success: function(response){
                        if(typeof response == 'string'){
                            $('#body').append(response);
                            verificaCarrinho(usuario)
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
                        } else {
                            verificaCarrinho(usuario)
                            $('#body').append(response);
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
                            })
                        }
                    }
                });
            }

            function verificaCarrinho(){
                var usuario = $('meta[name="user"]').attr('content');
                $('#carrinho').html("")

                $.ajax({
                    url: "/loja/verifica-carrinho",
                    method: 'post',
                    data: {
                        'usuario': usuario,
                    },
                    success: function(response){
                        $("#carrinho").append(response)
                    }
                });
            }


            $(document).ready(function(){
                var _token = $('meta[name="_token"]').attr('content');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': _token
                    }
                });

                verificaCarrinho()

            })
        </script>

        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
        @livewireScripts
    </body>

</html>

