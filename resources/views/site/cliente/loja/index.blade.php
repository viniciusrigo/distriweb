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
        @if (auth()->user())
            <meta name="user" content="{{ auth()->user()->id }}">
        @endif

        {{-- Title --}}
        <title>Padovani Bebidas - Loja</title>
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

    <body id="body" class="container-fluid">
        <div class="bg-warning w-100">
            <div class="d-flex row justify-content-center p-2 bg-warning">
                <div class="d-flex align-items-center col-md-3 col-1 mr-auto ml-auto">
                    <img class="rounded" src="{{ asset('logo.png') }}" width="45px" height="45px">
                    <a class="off nav-link text-white" href="{{ route('loja.index') }}">
                        <strong>Padovani Bebidas</strong>
                    </a>
                </div>
                <div class="d-flex col-md-3 col-5 justify-content-end align-items-center mr-auto ml-auto">
                    <div class="d-flex row justify-content-center">
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="off mr-3 ml-3">{{ auth()->user()->pontos }} Pontos</span>
                            <a class="off btn btn-dark rounded-pill mr-3 ml-3" href="{{ route('loja.carrinho') }}">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span style="position:absolute; margin-left:14px" id="carrinho" class="badge badge-info"></span>
                            </a>
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
            @if (count($pedido_aberto) != 0)
                @for ($i = 0; $i < count($pedido_aberto); $i++)    
                    <div style="color: #888888;" class="d-flex flex-wrap justify-content-center col-10 m-2 p-2 border border-danger rounded-pill">
                        <div style="font-size: 1.3rem;" class="d-flex justify-content-center col-md-12">
                            <span>Pedido <strong style="color: #f65454">#{{ $pedido_aberto[$i]->id }}</strong></span>
                        </div>
                        <div class="d-flex justify-content-center row col-md-12">
                            <div class="d-flex justify-content-center col-md-2">
                                <span>Valor: <strong style="color: #f65454">R${{ $pedido_aberto[$i]->total }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-center col-md-3">
                                <span>Chegada aprox. <strong style="color: #f65454">{{ $pedido_aberto[$i]->tempo_entrega }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-center col-md-2">
                                <span>Código <strong style="color: #f65454">{{ $pedido_aberto[$i]->codigo }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-center col-md-3">
                                <span style="color: #f65454">
                                    @php
                                        if($pedido_aberto[$i]->status == "n"){
                                            echo "<i class='fa-solid fa-hourglass-half'></i> AGUARDANDO RECEBER";
                                        } else if ($pedido_aberto[$i]->status == "s") {
                                            echo "<i class='fa-solid fa-box-open'></i> EM SEPARAÇÃO";
                                        } else {
                                            echo "<i class='fa-solid fa-motorcycle'></i> À CAMINHO";
                                        }
                                    @endphp
                                    
                                </span>
                            </div>
                        </div>
                        @if ($pedido_aberto[$i]->status == "n")   
                            <form id="cancelar" action="{{ route('loja.produtos.cancelar-pedido') }}" method="POST">
                                @csrf
                                <input type="hidden" name="pedido_id" value="{{ $pedido_aberto[$i]->id }}">
                            </form> 
                            <div style="font-size: 1.3rem;" class="d-flex justify-content-center col-md-12 mt-2">
                                <button form="cancelar" class="btn btn-danger rounded-pill">Cancelar Pedido</button>
                            </div>
                        @endif
                    </div>
                @endfor 
            @endif
            @if($produtos_desconto != [])
                <div style="height: auto" class="row col-12 col-md-12 justify-content-center bg-warning">
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
                                <img src="{{ file_exists('storage/variaveis_produtos/'.$produto["id"].'.png') ? url('storage/variaveis_produtos/'.$produto["id"].'.png') : url('storage/produtos/'.$produto["produto_id"].'.png') }}" alt="Card image cap" width="210px" height="210px">
                            </div>
                            <div class="card-body m-0 p-1">
                                <div style="height: 40px" class="d-flex justify-content-center align-items-center">
                                    <h6 style="font-size: 14px;" class="card-title m-0">{{ $produto["nome"] }} <strong>{{ $produto["variavel_nome"] }}</strong></h6>
                                </div>
                                <div style="font-size: 16px;" class="d-flex justify-content-center align-itens-center">
                                    <span class="mr-3" onclick="remover({{ $produto['id'] }})" style="color: red; font-size: 25px; cursor: pointer;">-</span>
                                    <input id="{{ $produto['id'] }}" style="border:none; outline: 0; width:30px; background-color:#f7f7f7;" type="text" name="quantidade" value="1">
                                    <span class="ml-3" onclick="adicionar({{ $produto['id'] }})" style="color: red; font-size: 25px; cursor: pointer;">+</span>
                                </div>
                                <div class="d-flex justify-content-center align-items-center mb-0">
                                    <span style="font-size: 19px; color:#f65454;"><strong>R${{ $produto["preco_promocao"] }}</strong></span><a style="padding: 1px 15px 1px 15px;border-radius: 20px" onclick="addCarrinho({{ auth()->user()->id }},{{ $produto['id'] }})" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @if ($produtos_fidelidade != [])
                <div style="height: auto" class="d-flex row justify-content-center col-12 bg-white">
                    <div style="font-size: 1.7rem; font-weight: 700;" class="d-flex justify-content-center col-md-10 col-12">
                        <span>Mais descontos ! <strong style="color: red; font-style:italic">15% OFF</strong></span>
                    </div>
                    <div style="overflow-x: auto;" class="scroll d-flex col-md-10 p-2">
                        @foreach ($produtos_fidelidade as $produto)
                            <div class="d-flex m-1 p-1 justify-content-center" style="width: 20rem; min-width: 20rem; background-color:#f7f7f7; border: none;">
                                <div class="d-flex justify-content-center align-items-center">
                                    <img src="{{ file_exists('storage/variaveis_produtos/'.$produto["id"].'.png') ? url('storage/variaveis_produtos/'.$produto["id"].'.png') : url('storage/produtos/'.$produto["produto_id"].'.png') }}" width="120px" height="120px">
                                </div>
                                <div class="m-0 p-1">
                                    <div style="height: 40px" class="d-flex justify-content-center align-items-center">
                                        <h6 style="font-size: 14px;" class="card-title m-0">{{ $produto["nome"] }} <strong>{{ $produto["variavel_nome"] }}</strong></h6>
                                    </div>
                                    <div style="font-size: 16px;" class="d-flex justify-content-center align-itens-center">
                                        <span class="mr-3" onclick="remover({{ $produto->id }})" style="color: red; font-size: 20px; cursor: pointer;">-</span>
                                        <input id="{{ $produto->id }}" style="border:none; outline: 0; width:20px; background-color:#f7f7f7;" type="text" name="quantidade" value="1">
                                        <span class="ml-3" onclick="adicionar({{ $produto->id }})" style="color: red; font-size: 20px; cursor: pointer;">+</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-0">
                                        <span style="font-size: 10px; color:#000000;">De <strong>R${{ $produto->preco }}</strong></span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center mb-0">
                                        <span style="font-size: 19px; color:#f65454;"><strong>R${{ number_format(abs((($produto->preco * 15) / 100) - $produto->preco), 2, '.', ',') }}</strong></span><a style="padding: 1px 15px 1px 15px;border-radius: 20px" onclick="addCarrinho({{ auth()->user()->id }},{{ $produto->id }})" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></a>
                                    </div>
                                    <div class="d-flex align-items-center mb-0">
                                        <span style="font-size: 12px; color:#bdbc69;">+ <strong>{{ $produto->pontos }}</strong> Pontos</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @if($combos != [])
                <div style="height: auto" class="row col-12 col-md-12 justify-content-center bg-warning">
                    <div style="font-size: 1.7rem; font-weight: 700;" class="d-flex justify-content-center col-md-10 col-12">
                        <span><strong style="color: rgb(255, 255, 255); font-style:irgb(255, 255, 255)ic">Combos</strong></span>
                    </div>
                    <div style="overflow-x: auto;" class="scroll d-flex justify-content-start col-md-10 col-12 p-2">
                        @foreach ($combos as $produto)
                            <div class="d-flex m-1 p-1 justify-content-center rounded" style="width: 30rem; min-width: 30rem; background-color:#F7F7F7; border: none;">
                                <div class="d-flex justify-content-center align-items-center">
                                    <img src="{{ file_exists('storage/variaveis_produtos/'.$produto["id"].'.png') ? url('storage/variaveis_produtos/'.$produto["id"].'.png') : url('storage/produtos/'.$produto["produto_id"].'.png') }}" width="250px" height="250px">
                                </div>
                                <div class="m-0 p-1">
                                    <div style="height: 135px" class="d-flex justify-content-center align-items-center">
                                        <h6 style="font-size: 18px;" class="card-title m-0">{{ $produto["nome"] }} <strong>{{ $produto["variavel_nome"] }}</strong></h6>
                                    </div>
                                    <div style="font-size: 22px;" class="d-flex justify-content-center align-itens-center">
                                        <span class="mr-3" onclick="remover({{ $produto['id'] }})" style="color: red; font-size: 30px; cursor: pointer;">-</span>
                                        <input id="{{ $produto['id'] }}" style="border:none; outline: 0; width:20px; background-color:#f7f7f7;" type="text" name="quantidade" value="1">
                                        <span class="ml-3" onclick="adicionar({{ $produto['id'] }})" style="color: red; font-size: 30px; cursor: pointer;">+</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center mb-0">
                                        <span style="font-size: 26px; color:#f65454;"><strong>R${{ $produto["preco"] }}</strong></span><a style="padding: 1px 15px 1px 15px;border-radius: 20px" onclick="addCarrinho({{ auth()->user()->id }},{{ $produto['id'] }})" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div style="height: auto" class="d-flex row justify-content-center col-12 col-md-12 bg-white p-3">
                <div style="border-radius: 10px;" class="row col-12 col-md-10 col-lg-8 justify-content-center bg-white">
                    <div class="d-flex justify-content-center align-items-center col-10 p-2">
                        <span><strong>Todos Produtos</strong></span>
                        <input id="input-busca" name="busca" class="form-control col-4 ml-auto" type="text" placeholder="Buscar produto">
                    </div>
                    <div id="geral" style="overflow: auto; height: 35rem" class="row justify-content-center">
                        
                    </div>
                </div>
            </div>
            <div style="height: auto; font-size: 1rem; font-weight: 500;" class="d-flex justify-content-center row col-12 col-md-12 bg-white p-2">
                <div class="d-flex justify-content-center align-items-center col-md-5 col-12">
                    <i class="fa-regular fa-copyright"></i><span class="ml-2">Copyright 2024 | Desenvolvido por <a href="https://wa.me/5543984220431" target="_blank">VRigo</a></span> 
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
    
                verificaCarrinho()
                buscarProdutos()

                $('#input-busca').keypress(function(e){
                    if(e.which == 13){
                        $('#geral').html("");
                        buscarProdutos(this.value)
                    }
                })
    
            })
            
            function buscarProdutos(produto){
                $.ajax({
                    url: "/loja/produtos-ajax",
                    method: 'post',
                    data: {
                        busca: produto
                    },
                    success: function(response){
                        for(var i=0;i < response.length; i++){
                            let codigo = response[i]['codigo_barras']
                            let id = response[i]['id']
                            let produto_id = response[i]['produto_id']
                            let preco = response[i]['preco']
                            let user_id = $('meta[name="user"]').attr('content')
                            var html = '<div class="card d-flex m-1 p-0 justify-content-center col-5 col-md-3 col-lg-2" style="height: 250px; border: none; background-color: #f7f7f7;">'
                            html += `<div class="d-flex justify-content-center"><img src="{{ url('storage/variaveis_produtos/${id}.png') }}" alt="Card image cap" width="130px" height="130px"></div>`
                            html += '<div class="card-body m-0 p-1">'
                            html += '<div style="height: 43px" class="d-flex justify-content-center align-items-center"><h6 style="font-size: 14px;" class="card-title m-0">'+response[i]['nome']+' <strong>'+response[i]['variavel_nome']+'</strong></h6></div>'
                            html += '<div style="font-size: 16px;" class="d-flex justify-content-center align-itens-center">'
                            html += `<span class="mr-3" onclick="removerg(${id})" style="color: red; font-size: 25px; cursor: pointer;">-</span>`
                            html += `<input id="g${id}" style="border:none; outline: 0; width:30px;background-color: #f7f7f7;" type="text" name="quantidade" value="1">`
                            html += `<span class="ml-3" onclick="adicionarg(${id})" style="color: red; font-size: 25px; cursor: pointer;">+</span>`
                            html += '</div>'
                            html += '<div class="d-flex justify-content-center align-items-center mb-0">'
                            html += `<span class="text-primary" style="font-size: 19px;"><strong>R$${preco}</strong></span><a style="padding: 1px 15px 1px 15px;border-radius: 20px" onclick="addCarrinhog(${user_id},${id})" class="add btn btn-primary ml-auto"><i class="fa-solid fa-cart-shopping"></i></a>`
                            html += '</div>'
                            html += '</div></div>'

                            $('#geral').append(html);
                        }
                    }
                });
            }

            function adicionarg(id){
                var add = parseFloat($('#g'+id).val()) + 1
                $('#g'+id).val(add)
            }

            function removerg(id){
                var add = parseFloat($('#g'+id).val())
                if (add <= 1){
                    return $('#g'+id).val(1)
                }
                var add = add - 1
                $('#g'+id).val(add)
            }

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

            function addCarrinhog(user_id, id){
                var quantidade = $('#g'+id).val()
                var usuario = user_id
                var variavel_produto_id = id

                $.ajax({
                    url: "/loja/add-carrinho",
                    method: 'post',
                    data: {
                        'usuario': usuario,
                        'variavel_produto_id': variavel_produto_id,
                        'quantidade': quantidade,
                        'carrinho': true
                    },
                    success: function(response){
                        if(response == true){
                            verificaCarrinho(usuario)
                            toastr.success('Produto adicionado ao carrinho')
                        } else {
                            alert(response)
                            verificaCarrinho(usuario)
                            toastr.error('Quantidade insuficiente')
                        }
                    }
                });
            }

            function addCarrinho(user_id, id){
                var quantidade = $('#'+id).val()
                var usuario = user_id
                var variavel_produto_id = id

                $.ajax({
                    url: "/loja/add-carrinho",
                    method: 'post',
                    data: {
                        'usuario': usuario,
                        'variavel_produto_id': variavel_produto_id,
                        'quantidade': quantidade,
                        'carrinho': true
                    },
                    success: function(response){
                        if(response == true){
                            verificaCarrinho(usuario)
                            toastr.success('Produto adicionado ao carrinho')
                        } else {
                            verificaCarrinho(usuario)
                            toastr.error('Quantidade insuficiente')
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
                        $("#carrinho").html("")
                        $("#carrinho").append(response)
                    }
                });
            }


        </script>

        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    </body>

</html>

