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
            @media (max-width: 576px){
                table {
                    font-size: 12px;
                }
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

    </head>

    <body class="container-fluid">
        @if (session('error')) 
        <div style="background: #ff9b9b; border-left: 8px solid #ff0202;" class="alert hide">
            <span style="color: #ce0000;" class="fas fa-solid fa-xmark"></span>
            <span style="color: #ce0000;" class="msg">{{ session('error') }}</span>
        </div>
        @endif
        @if (session('alerta'))
        <div style="background: #ffdb9b; border-left: 8px solid #ffa502;" class="alert hide">
            <span style="color: #ce8500;" class="fas fa-exclamation-circle"></span>
            <span style="color: #ce8500;" class="msg">{{ session('alerta') }}</span>
        </div>
        @endif
        @if (session('success'))
        <div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
            <span style="color: #ffffff;" class="fas fa-solid fa-circle-check"></span>
            <span style="color: #ffffff;" class="msg">{{ session('success') }}</span>
        </div>
        @endif
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
                            <a style="text-decoration: none;" class="a-link mr-1 ml-1 pl-3 pr-3" href="{{route('logincliente.destroy')}}"><span>{{ auth()->user()->name }}</span></i></a> 
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div style="border-radius: 10px; background-color: white;" class="row col-12 col-md-8 justify-content-center p-2 mt-2">
                <div class="d-flex justify-content-center col-12">
                    Meu Carrinho
                </div>
                    @if(count($produtos) != 0)
                        <div style="height: 55vh;overflow: auto;" class="col-12 col-md-8 p-0 border-right">
                            <table class="table-sm col-12">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Produto</th>
                                        <th>QTD</th>
                                        <th>Valor</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($produtos as $produto)
                                        <tr>
                                            <td><img style="background-color: #dedede; border-radius: 10px" src="{{ url('storage/produtos/'.$produto->codigo_barras.'.png') }}" width="50px" height="50px"></td>
                                            <td>{{ $produto->nome }}</td>
                                            <td>{{ $produto->qtd }}x</td>
                                            <td>R${{ number_format($produto->total_produto, 2, '.','. ') }}</td>
                                            <td>
                                                <form action="{{ route('loja.remover-carrinho') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                                                    <button type="submit" style="color: red;" class="btn">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> 
                        <div class="p-1 col-12 col-md-4">
                            <div style="font-size: 13px" class="col-12">
                                <div>
                                    <span>Entrega: {{ auth()->user()->logradouro }} - {{ auth()->user()->bairro }}</span>
                                </div>
                                <div>
                                    <span>Para: {{ auth()->user()->name }}</span>
                                </div>
                            </div>
                            <div style="font-size: 13px" class="col-12">
                                <div class="input-group">
                                    <textarea onkeypress="zona()" class="form-control" id="novo_endereco" name="novo_endereco" aria-label="With textarea" placeholder="Outro endereço caso não esteja na {{ auth()->user()->logradouro }}"></textarea>
                                </div>                              
                            </div>                        
                            <div id="zona" style="display: none;" class="justify-content-center col-12 p-1">
                                <div class="col-5 d-flex justify-content-center p-1">
                                    <select form="form_carrinho" class="form-control" name="zona">
                                        <option value="">Zona</option>
                                        <option value="1">Norte</option>
                                        <option value="2">Sul</option>
                                        <option value="3">Leste</option>
                                        <option value="4">Oeste</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex col-md-12 p-1">
                                <form id="form_carrinho" class="d-flex col-12 flex-wrap align-items-center " action="{{ route('loja.concluir-pedido') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="cpf" value="{{ auth()->user()->cpf }}">
                                    <input type="hidden" name="users_id" value="{{ auth()->user()->id }}">
                                    <div class="d-flex col-12 justify-content-center">  
                                        <select class="form-control p-1" name="forma_pagamentos_id" id="forma_pagamentos_id" required>
                                            <option value="">Pagar</option>
                                            <option value="2">Dinheiro</option>
                                            <option value="3">PIX</option>
                                            <option value="credito">Crédito</option>
                                            <option value="debito">Débito</option>
                                        </select>
                                    </div>
                                    <div class="d-flex col-12 justify-content-center mt-2">
                                        <input form="form_carrinho" class="ml-2" type="text" name="dinheiro" id="dinheiro" style="display: none;border: none;background-color:#f3f3f3;" placeholder="Troco para R$">
                                    </div>
                                    </form>
                            </div>
                            <div class="row col-md-12">
                                <div class="col-12">
                                    <span>Pontos gerados: </span><input style="border: none; width: 50px; background-color: #ffffff; color: black;" type="text" value="{{ $pontos }}" disabled>
                                </div>
                                <div class="col-12">
                                    <span>R$</span><input style="border: none; width: 70px; background-color: #ffffff; color: black;" type="text" value="{{ number_format($total, 2, '.', ',') }}" disabled>
                                </div>
                                <div class="col-12">
                                    <span class="ml-auto">Entrega: </span>
                                    @if (auth()->user()->zona_id == 1)
                                        <span >R$</span><input style="border: none; width: 50px; background-color: #ffffff; color: black;" type="text" value="{{$zona["entrega"]}}" disabled>
                                    @endif
                                    @if (auth()->user()->zona_id == 2)
                                        <span >R$</span><input style="border: none; width: 50px; background-color: #ffffff; color: black;" type="text" value="20.00" disabled>
                                    @endif
                                    @if (auth()->user()->zona_id == 3)
                                        <span >R$</span><input style="border: none; width: 50px; background-color: #ffffff; color: black;" type="text" value="15.00" disabled>
                                    @endif
                                    @if (auth()->user()->zona_id == 4)
                                        <span>R$</span><input style="border: none; width: 50px; background-color: #ffffff; color: black;" type="text" value="15.00" disabled>
                                    @endif
                                </div>
                            </div>
                            <button class="btn btn-success col-md-12 mt-2" form="form_carrinho" type="submit">Fazer Pedido</button>
                        </div>                      
                    @else
                        <div style="height: 55vh;overflow: auto;" class="d-flex justify-content-center align-items-center col-12 p-0">
                            <div>
                                <div class="d-flex justify-content-center col-12">
                                    <img src="{{ asset('shopping-empty.png') }}" alt="" width="150px" height="150px">
                                </div>
                                <div class="d-flex justify-content-center col-12">
                                    <span>Nenhum produto encontrado no seu carrinho, <a href="{{ route('loja.index') }}">Comprar</a></span>
                                </div>
                            </div>
                        </div>
                    @endif
                
            </div>
            <div style="border-radius: 10px; background-color: white; height: 20vh;" class="row col-12 col-md-8 justify-content-center p-2 mt-2">
                <div class="d-flex justify-content-center col-12">
                    Ofertas Exclusivas, aproveite !
                </div>
            </div>
        </div>

        <script>

            $(document).ready(function(){
                var _token = $('meta[name="_token"]').attr('content');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': _token
                    }
                });    

                $('#novo_endereco').on("keyup", function () {
                    var n = $(this).val().length;
                    if(n > 0){
                        document.getElementById("zona").style.display = "flex";
                    } else {
                        document.getElementById("zona").style.display = "none";
                    }
                });

                document.getElementById("forma_pagamentos_id").onchange = function(){
                    if (document.getElementById("forma_pagamentos_id").value == 2){
                        document.getElementById("dinheiro").style.display = "block";
                    } else {
                        document.getElementById("dinheiro").style.display = "none"
                    }
                }

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
                
            })
        </script>

        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    </body>

</html>

