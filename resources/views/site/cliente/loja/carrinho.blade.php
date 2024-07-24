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
        
        <link rel="icon" href="{{ asset('logo.png') }}">

        {{-- Title --}}
        <title>Padovani Bebidas - Carrinho</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <link rel="stylesheet" href="{{ asset('css/cliente-principal.css') }}">
        <style>

        </style>

    </head>

    <body class="container-fluid">
        @if (session('error')) 
            <div style="background: #ff0202; border-left: 8px solid #ff0202;" class="alert hide">
                <span style="color: #ffffff;" class="fas fa-solid fa-xmark"></span>
                <span style="color: #ffffff;" class="msg">{{ session('error') }}</span>
            </div>
        @endif
        @if (session('success'))
            <div style="background: #9bd47a; border-left: 8px solid #2b771c;" class="alert hide">
                <span style="color: #ffffff;" class="fas fa-solid fa-circle-check"></span>
                <span style="color: #ffffff;" class="msg">{{ session('success') }}</span>
            </div>
        @endif
        <div class="bg-warning w-100">
            <div class="d-flex row justify-content-center p-2 bg-warning">
                <div class="d-flex align-items-center col-md-3 col-1 mr-auto ml-auto">
                    <a href="{{ route('loja.index') }}"><img class="rounded" src="{{ asset('logo.png') }}" width="45px" height="45px"></a>
                    <a class="off nav-link text-white" href="{{ route('loja.index') }}">
                        <strong>Padovani Bebidas</strong>
                    </a>
                </div>
                <div class="d-flex col-md-3 col-5 justify-content-end align-items-center mr-auto ml-auto">
                    <div class="d-flex row justify-content-center">
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="off mr-3 ml-3">{{ auth()->user()->pontos }} Pontos</span>
                            <ul class="navbar-nav mr-3 ml-3">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ auth()->user()->name }}
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="#">Minhas Compras</a>
                                        <a class="dropdown-item" href="#">Meu dados</a>
                                    </div>
                                </li>
                            </ul>
                            <a class="btn btn-danger rounded-pill mr-1 ml-1 pl-3 pr-3" href="{{ route('logincliente.destroy') }}"><span class="">Sair</span></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            @if(count($produtos) != 0)
                <div style="border-radius: 10px; background-color: white;" class="row col-12 col-md-6 justify-content-center p-2 m-2">
                    <div class="d-flex justify-content-start col-12 border-bottom text-warning">
                        <strong>Endereço para Entrega</strong>
                    </div>
                    <div class="d-flex justify-content-start align-items-center col-12 mt-2">
                        <i class="fa-solid fa-location-dot"></i>&nbsp;&nbsp;<strong>{{ auth()->user()->logradouro }} - {{ auth()->user()->bairro }}</strong>
                        <button id="novo-endereco" class="btn-sm btn-danger rounded-pill ml-auto">Não estou neste endereço</button>
                    </div>
                    <div id="div-novo-endereco" class="d-none justify-content-center align-items-center row col-12 mt-2">
                        <div class="d-flex justify-content-center col-md-6 col-12">
                            <input form="form_carrinho" class="form-control" id="novo_endereco" name="novo_endereco" placeholder="Digite aqui caso não esteja na {{ auth()->user()->logradouro }}"></input>
                        </div>
                        <div class="d-flex justify-content-center col-md-2">
                            <div style="display: none">
                                @foreach ($zonas as $zona)
                                    <input value="{{ $zona->entrega }}" id="{{ $zona->id }}">
                                @endforeach
                            </div>                      
                            <div class="d-flex justify-content-center col-md-12 p-1">
                                <div id="zona" style="display: none;" class="justify-content-center col-md-12 p-1">
                                    <div class="d-flex justify-content-center p-1">
                                        <select id="select_zona" form="form_carrinho" class="form-control" name="zona_id">
                                            @foreach ($zonas as $zona)
                                                <option value="{{ $zona->id }}">{{ $zona->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>   
                    </div>
                </div>
            @endif
            <div style="border-radius: 10px; background-color: white;" class="row col-12 col-md-6 justify-content-center p-2 m-2">
                <div class="d-flex justify-content-start col-12 border-bottom text-warning">
                    <strong>Meu Carrinho</strong>
                </div>
                @if(count($produtos) != 0)
                    <div style="height: 40vh;overflow: auto;" class="col-12 col-md-12 p-0 ">
                        <table class="table-sm col-md-12">
                            <thead>
                                <tr class="bg-warning rounded">
                                    <th class="text-warning">#</th>
                                    <th class="text-white">Produto</th>
                                    <th class="text-white">Valor</th>
                                    <th class="text-warning">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($produtos as $produto)
                                    <tr>
                                        <td><img style="background-color: #dedede; border-radius: 10px" src="{{ url('storage/variaveis_produtos/'.$produto->id.'.png') }}" width="50px" height="50px"></td>
                                        <td>{{ $produto->nome }} {{ $produto->variavel_nome }}</td>
                                        <td>R${{ number_format($produto->total_produto, 2, '.','. ') }}</td>
                                        <td>
                                            <form action="{{ route('loja.remover-carrinho') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="variavel_produto_id" value="{{ $produto->id }}">
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

            @if(count($produtos) != 0)
                <div style="border-radius: 10px; background-color: white;" class="row col-12 col-md-6 justify-content-center p-2 m-2">
                    <div class="d-flex justify-content-start col-12 border-bottom text-warning">
                        <strong>Informações</strong>
                    </div>
                    <div class="p-1 col-12 col-md-6">
                        <div class="row col-md-12">
                            <div class="col-12">
                                <span>Pontos Troca: {{ $pontos }}</span>
                            </div>
                            <div class="col-12">
                                <span>Meus Pontos: </span><input form="form_carrinho" style="border: none; width: 50px; background-color: #ffffff; color: black;" type="text" value="{{ auth()->user()->pontos }}" disabled>
                            </div>
                            <div class="col-12">
                                <span>Pontos Ganhos: {{ $total != null ? round($total, 0) : "" }} </span>
                            </div>
                            <div class="col-12">
                                <span>Produtos: R$ </span><input id="produtos" style="border: none; width: 70px; background-color: #ffffff; color: black;" type="text" value="{{ $total != null ? number_format($total, 2, ',', '') : "" }}" disabled>
                            </div>
                            <div class="col-12">
                                <span class="ml-auto">Entrega: R$</span>
                                @foreach ($zonas as $zona)
                                    @if ($zona->id == auth()->user()->zona_id)
                                        <span id="span-zona">{{ number_format($zona["entrega"], 2, ',', '') }}</span>
                                        <input form="form_carrinho" id="zona_padrao" name="frete" style="border: none; width: 50px; background-color: #ffffff; color: black;" type="hidden" value="{{ number_format($zona["entrega"], 2, ',', '') }}" readonly>
                                    @endif
                                @endforeach
                                <input form="form_carrinho" id="zona_nova" name="frete" style="border: none; width: 50px; background-color: #ffffff; color: black;" type="hidden" readonly>
                            </div>
                        </div>
                    </div>
                    <div  class="d-flex justify-content-start align-items-center col-md-12 p-2">
                        <span><strong>Forma de Pagamento: </strong></span>
                        <form id="form_carrinho" class="d-flex justify-content-center col-md-6 flex-wrap align-items-center" action="{{ route('loja.concluir-pedido') }}" method="POST">
                            @csrf
                            <input type="hidden" name="cpf" value="{{ auth()->user()->cpf }}">
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <input name="pontos_troca" type="hidden" value="{{ $pontos }}">
                            <input name="pontos" type="hidden" value="{{ $total != null ? round($total, 0) : "" }}">
                            <input name="lucro" type="hidden" value="{{ $lucro }}">
                            <div class="d-flex col-md-6 justify-content-center">  
                                <select class="form-control p-1" name="forma_pagamento_id" id="forma_pagamento_id" required>
                                    <option value="">Pagar</option>
                                    <option value="2">Dinheiro</option>
                                    <option value="3">PIX</option>
                                    <option value="credito">Crédito</option>
                                    <option value="debito">Débito</option>
                                </select>
                            </div>
                            <div class="d-flex col-md-6 justify-content-center">
                                <input class="form-control" type="text" name="dinheiro" id="dinheiro" style="display: none;border: none;background-color:#f3f3f3;" placeholder="Troco para R$">
                            </div>
                        </form>
                    </div>
                    <div style="background-color: #e4e4e4;" class="d-flex justify-content-center align-items-center row col-md-12">
                        <div style="font-size: 30px;" class="col-md-8 col-12">
                            <span>Total: R$</span><span id="total-span"></span>
                            <input form="form_carrinho" id="total" name="total" style="border: none; width: 95px; background-color: #e4e4e4; color: black;" type="hidden">
                        </div>
                        <div class="col-md-3 col-12">
                            <button class="btn btn-primary col-md-12" form="form_carrinho" type="submit">Fazer Pedido</button>
                        </div>
                    </div>
                </div>
            @endif
            <div style="border-radius: 10px; background-color: white; height: auto;" class="row col-12 col-md-8 justify-content-center p-2 mt-2 mb-2">
                <div class="d-flex justify-content-center col-12">
                    <span>Ofertas Exclusivas, aproveite !</span>
                </div>
                <div style="overflow-x: auto;" class="scroll d-flex col-md-11 p-2">
                    @foreach ($produtos_promocao as $produto)
                        <form id="form-add-produto-{{ $produto->id }}" action="{{ route('loja.add-carrinho') }}" method="POST">
                            @csrf
                            
                            <input type="hidden" name="variavel_produto_id" value="{{ $produto->id }}">
                            <input type="hidden" name="usuario" value="{{ auth()->user()->id }}">
                        </form>
                        <div class="d-flex m-1 p-1 justify-content-center" style="width: 20rem; min-width: 20rem; background-color:#f7f7f7; border: none;">
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="{{ url('storage/variaveis_produtos/'.$produto["id"].'.png') }}" width="120px" height="120px">
                            </div>
                            <div class="m-0 p-1">
                                <div style="height: 40px" class="d-flex justify-content-center align-items-center">
                                    <h6 style="font-size: 14px;" class="card-title m-0">{{ $produto->nome }} <strong>{{ $produto->variavel_nome }}</strong></h6>
                                </div>
                                <div style="font-size: 16px;" class="d-flex justify-content-center align-itens-center">
                                    <span class="mr-3" onclick="remover({{ $produto->id }})" style="color: red; font-size: 20px; cursor: pointer;">-</span>
                                    <input form="form-add-produto-{{ $produto->id }}" id="c{{ $produto->id }}" style="border:none; outline: 0; width:20px; background-color:#f7f7f7;" type="text" name="quantidade" value="1">
                                    <span class="ml-3" onclick="adicionar({{ $produto->id }})" style="color: red; font-size: 20px; cursor: pointer;">+</span>
                                </div>
                                <div class="d-flex align-items-center mb-0">
                                    <span style="font-size: 10px; color:#000000;">De <strong>R${{ $produto->preco }}</strong></span>
                                </div>
                                <div class="d-flex justify-content-center align-items-center mb-0">
                                    <span style="font-size: 19px; color:#f65454;"><strong>R${{ $produto->preco_promocao }}</strong></span><button form="form-add-produto-{{ $produto->id }}" type="submit" style="padding: 1px 15px 1px 15px;border-radius: 20px" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div style="height: auto; font-size: 1rem; font-weight: 500; bottom: 0;" class="d-flex justify-content-center row col-12 col-md-12 bg-white p-2">
                <div class="d-flex justify-content-center align-items-center col-md-5 col-12">
                    <i class="fa-regular fa-copyright"></i><span class="ml-2">Copyright 2024 | Desenvolvido por <a href="https://wa.me/5543984220431" target="_blank">VRigo</a></span> 
                </div>   
            </div>
        </div>

        <script>

            function adicionar(id){
                var add = parseFloat($('#c'+id).val()) + 1
                $('#c'+id).val(add)
            }

            function remover(id){
                var add = parseFloat($('#c'+id).val())
                if (add <= 1){
                    return $('#c'+id).val(1)
                }
                var add = add - 1
                $('#c'+id).val(add)
            }

            $(document).ready(function(){
                var _token = $('meta[name="_token"]').attr('content');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': _token
                    }
                });    

                document.getElementById("zona_nova").style.display = "none";
                $('#zona_nova').prop('disabled',true);

                $("#novo-endereco").on("click", function(){
                    $("#div-novo-endereco").removeClass("d-none")
                    $("#div-novo-endereco").addClass("d-flex")
                })

                var produtos = parseFloat($('#produtos').val())
                var zona_padrao = parseFloat($('#zona_padrao').val())
                $("#total").val(produtos + zona_padrao)
                $("#total-span").html((produtos + zona_padrao).toFixed(2))

                $('#novo_endereco').on("keyup", function () {
                    var n = $(this).val().length;
                    if(n > 0){
                        document.getElementById("zona").style.display = "flex";
                        document.getElementById("zona_padrao").style.display = "none";
                        document.getElementById("zona_nova").style.display = "inline";
                        $('#select_zona').prop('required',true);
                        $('#zona_padrao').prop('disabled',true);
                        $('#zona_nova').prop('disabled',false);
                    } else {
                        document.getElementById("zona").style.display = "none";
                        document.getElementById("zona_padrao").style.display = "inline";
                        document.getElementById("zona_nova").style.display = "none";
                        $('#select_zona').prop('required',false);
                        var produtos = parseFloat($('#produtos').val())
                        var zona_padrao = parseFloat($('#zona_padrao').val())
                        $("#total").val(produtos + zona_padrao)
                        $('#zona_padrao').prop('disabled',false);
                        $('#zona_nova').prop('disabled',true);
                    }
                });

                $('#select_zona').on("change", function(){
                    var z = $(this).val()
                    if(z == 1){
                        var valor = $("#1").val()
                    }
                    if(z == 2){
                        var valor = $("#2").val()
                    }
                    if(z == 3){
                        var valor = $("#3").val()
                    }
                    if(z == 4){
                        var valor = $("#4").val()
                    }
                    if(z == 5){
                        var valor = $("#5").val()
                    }
                    document.getElementById("zona_nova").style.display = "inline";
                    $("#zona_nova").val(valor)
                    $("#span-zona").html("")
                    $("#span-zona").html(valor)
                    var produtos = parseFloat($('#produtos').val())
                    var zona_nova = parseFloat($('#zona_nova').val())

                    $("#total").val(produtos + zona_nova)
                    $("#total-span").html((produtos + zona_nova).toFixed(2))
                })

                document.getElementById("forma_pagamento_id").onchange = function(){
                    if (document.getElementById("forma_pagamento_id").value == 2){
                        document.getElementById("dinheiro").style.display = "block";
                    } else {
                        document.getElementById("dinheiro").style.display = "none"
                    }
                }
                
            })
        </script>

        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    </body>

</html>

