<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- Base Meta Tags --}}
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="_token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('logo.png') }}">

        {{-- Title --}}
        <title>Padovani Bebidas</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');
            @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
            body{
                font-family: 'Montserrat', sans-serif; 
                background: #ececec;
            }
            @media (max-width: 575.98px) {
                .off{
                    display: none;
                }
            }
            @media (min-width: 1200px) {
                .off{
                    display: flex;
                }
            }
            .scroll::-webkit-scrollbar{
                height: 9px;
            }
            ::-webkit-scrollbar{
                width: 7px;
            }
            ::-webkit-scrollbar-thumb{
                border-radius: 30px;
                background-color: #FFC107;
            }
            ::-webkit-scrollbar-thumb:hover{
                border-radius: 30px;
                background-color: #c39508;
            }

        </style>

    </head>

    <body class="container-fluid">
        <div class="bg-warning w-100">
            <div class="d-flex row justify-content-center p-2 bg-warning">
                <div class="d-flex align-items-center col-md-3 col-1 mr-auto ml-auto">
                    <img class="rounded" src="{{ asset('logo.png') }}" width="45px" height="45px">
                    <a class="off nav-link text-white" href="{{ route('welcome') }}">
                        <strong>Padovani Bebidas</strong>
                    </a>
                </div>
                <div class="d-flex col-md-3 col-5 justify-content-end align-items-center mr-auto ml-auto">
                    <div class="d-flex row justify-content-center">
                        <div class="d-flex justify-content-center align-items-center">
                            <a class="off btn btn-dark rounded-pill mr-1 ml-1 pl-3 pr-3" href="{{ route('logincliente') }}"><i class="fa-solid fa-cart-shopping"></i></a>
                            <a class="btn btn-dark rounded-pill mr-1 ml-1 pl-3 pr-3" href="{{ route('logincliente') }}"><span class="">Fazer Login</span></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div style="height: auto" class="row col-12 col-md-12 justify-content-center bg-warning">
                @if($produtos_desconto != [])
                    <div class="d-flex justify-content-center align-items-center col-md-4 col-12">
                        <div class="d-flex flex-wrap justify-content-center">
                            <img src="{{ asset('desconto.png') }}" alt="" width="150px" height="150px" class="">
                            <span style="font-size: 2.9rem; font-weight: 500;" class="d-flex justify-content-center col-md-12"><strong>Vamos de Promoção !?</strong></span>
                            <span style="font-size: 1.8rem; font-weight: 100;" class="d-flex justify-content-center col-md-9"><strong>Aqui seu dinheiro vale mais</strong></span>
                        </div>
                    </div>
                    <div style="overflow-x: auto;" class="scroll d-flex justify-content-start col-md-5 col-12 p-2">
                        @foreach ($produtos_desconto as $produto)
                        <div class="card d-flex ml-2 p-1 justify-content-center" style="width: 12rem; min-width: 12rem; background-color:#f7f7f7; border: none;">
                            <div class="d-flex justify-content-center">
                                <div style="position: absolute;" class="d-flex col-12">
                                    <span class="badge badge-danger ml-auto">{{ abs(round((($produto["preco_promocao"]* 100) / $produto["preco"]) - 100, 2)) == "0" ? "" : abs(round((($produto["preco_promocao"]* 100) / $produto["preco"]) - 100, 2))."%" }}</span>
                                </div>
                                <img src="{{ url('storage/variaveis_produtos/'.$produto["id"].'.png') }}" alt="Card image cap" width="210px" height="210px">
                            </div>
                            <div class="card-body m-0 p-1">
                                <div style="height: 40px" class="d-flex justify-content-center align-items-center">
                                    <h6 style="font-size: 14px;" class="card-title m-0">{{ $produto["nome"] }} <strong>{{ $produto["variavel_nome"] }}</strong></h6>
                                </div>
                                <div style="font-size: 16px;" class="d-flex justify-content-center align-itens-center">
                                    <span class="mr-3" onclick="addCarrinho()" style="color: red; font-size: 25px; cursor: pointer;">-</span>
                                    <input style="border:none; outline: 0; width:30px; background-color:#f7f7f7;" type="text" name="quantidade" value="1">
                                    <span class="ml-3" onclick="addCarrinho()" style="color: red; font-size: 25px; cursor: pointer;">+</span>
                                </div>
                                <div class="d-flex justify-content-center align-items-center mb-0">
                                    <span style="font-size: 19px; color:#f65454;"><strong>R${{ number_format($produto["promocao"] == "s" ? $produto["preco_promocao"] : $produto["preco"], 2, ',', '.') }}</strong></span><a style="padding: 1px 15px 1px 15px;border-radius: 20px" onclick="addCarrinho()" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div style="height: auto; border-bottom: 1px solid #FFC107" class="off justify-content-center col-md-12 bg-white p-2">
                <div style="font-size: 1.7rem; font-weight: 500;" class="mr-3"><i class="fa-solid fa-truck-fast"></i> Entrega Rápida</div>
                <div style="font-size: 1.7rem; font-weight: 500;" class="ml-5 mr-5"><i class="fa-solid fa-bottle-water"></i> + de 2 Mil Produtos</div>
                <div style="font-size: 1.7rem; font-weight: 500;" class="ml-3"><i class="fa-regular fa-circle-user"></i> + de 500 Clientes Cadastrados</div>
            </div>
            <div style="height: auto" class="d-flex row justify-content-center col-12 bg-white">
                @if ($produtos_fidelidade != []) 
                    <div style="font-size: 1.7rem; font-weight: 700;" class="d-flex justify-content-center col-md-10 col-12">
                        <span>Valor exclusivo para Clientes Cadastrados ! <strong style="color: red; font-style:italic">15% OFF</strong></span>
                    </div>
                    <div style="overflow-x: auto;" class="scroll d-flex col-md-10 p-2">
                        @foreach ($produtos_fidelidade as $produto)
                            <div class="d-flex m-1 p-1 justify-content-center" style="width: 20rem; min-width: 20rem; background-color:#f7f7f7; border: none;">
                                <div class="d-flex justify-content-center align-items-center">
                                    <img src="{{ url('storage/variaveis_produtos/'.$produto["id"].'.png') }}" width="120px" height="120px">
                                </div>
                                <div class="m-0 p-1">
                                    <div style="height: 40px" class="d-flex justify-content-center align-items-center">
                                        <h6 style="font-size: 14px; font-weight: 600;" class="card-title m-0">{{ $produto["nome"] }} <strong>{{ $produto["variavel_nome"] }}</strong></h6>
                                    </div>
                                    <div style="font-size: 16px;" class="d-flex justify-content-center align-itens-center">
                                        <span class="mr-3" onclick="addCarrinho()" style="color: red; font-size: 20px; cursor: pointer;">-</span>
                                        <input style="border:none; outline: 0; width:20px; background-color:#f7f7f7;" type="text" name="quantidade" value="1">
                                        <span class="ml-3" onclick="addCarrinho()" style="color: red; font-size: 20px; cursor: pointer;">+</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center mb-0 mt-2">
                                        <span style="font-size: 19px; color:#f65454;"><strong>R${{ number_format(abs((($produto["preco"] * 15) / 100) - $produto["preco"]), 2, ',', '.') }}</strong></span><a style="padding: 1px 15px 1px 15px;border-radius: 20px" onclick="addCarrinho()" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></a>
                                    </div>
                                    <div class="d-flex align-items-center mb-0">
                                        <span style="font-size: 12px; color:#bdbc69;">+ <strong>{{ $produto["pontos"] }}</strong> Pontos</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div style="height: auto" class="d-flex row justify-content-center col-12 col-md-12 bg-warning p-3">
                <div style="border-radius: 10px;" class="row col-12 col-md-8 justify-content-center bg-white">
                    <div class="d-flex justify-content-center align-items-center col-10 p-2">
                        <span><strong>Todos Produtos</strong></span>
                    </div>
                    <div id="geral" style="overflow: auto; height: 35rem" class="row justify-content-center">
                        @foreach ($produtos as $produto)
                            @if ($produto->promocao != "s")
                                <div class="card d-flex m-1 p-0 justify-content-center" style="width: 10rem; height: 250px; border: none; background-color: #f7f7f7;">
                                    <div class="d-flex justify-content-center">
                                        <img src="{{ url('storage/variaveis_produtos/'.$produto->variavel_produto_id.'.png') }}" alt="Card image cap" width="130px" height="130px">
                                    </div>
                                    <div class="card-body m-0 p-1">
                                        <div style="height: 40px" class="d-flex justify-content-center align-items-center">
                                            <h6 style="font-size: 14px;" class="card-title m-0">{{ $produto->nome }}</h6>
                                        </div>
                                        <div style="font-size: 16px;" class="d-flex justify-content-center align-itens-center">
                                            <span class="mr-3" onclick="addCarrinho()" style="color: red; font-size: 25px; cursor: pointer;">-</span>
                                            <input style="border:none; outline: 0; width:30px;background-color: #f7f7f7;" type="text" name="quantidade" value="1">
                                            <span class="ml-3" onclick="addCarrinho()" style="color: red; font-size: 25px; cursor: pointer;">+</span>
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center mb-0">
                                            <span class="text-primary" style="font-size: 19px;"><strong>R${{ number_format($produto->promocao == "s" ? $produto->preco_promocao : $produto->preco, 2, ',', '.') }}</strong></span><a style="padding: 1px 15px 1px 15px;border-radius: 20px" onclick="addCarrinho()" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div style="height: auto; font-size: 1rem; font-weight: 500;" class="d-flex justify-content-center row col-12 col-md-12 bg-white p-2">
                <div class="d-flex justify-content-center align-items-center col-md-5 col-12">
                    <i class="fa-regular fa-copyright"></i><span class="ml-2">Copyright 2024 | Desenvolvido por <a href="https://wa.me/5543984220431" target="_blank">VRigo</a></span> 
                </div>
                <div class="off justify-content-center col-5">
                    <a href="/login">Login Admin</a>
                </div>     
            </div>
        </div>
        
        <script>
            function addCarrinho(){
                window.location.href = "/login-cliente";
            }
        </script>

        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    </body>

</html>

