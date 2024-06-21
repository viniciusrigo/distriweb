@extends('adminlte::page')

@section('title', 'DW - Pedidos')

@section('content_header')

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
    <style>
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
@stop

@section('content')
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
    <div class="conteiner row d-flex justify-content-center align-items-center vh-100">
        @isset($pedidos)
            
            <div style="background-color: #ffffff; overflow:auto;" class="col-md-3 vh-100">
                @for ($i = 0; $i < count($pedidos); $i++)
                    @if ($pedidos[$i]["status"] == 'n')
                        <div class="col-md-12 mt-2">
                            <div class="card collapsed-card">
                                <div class="card-header">
                                    <span>Nº <strong>{{ $pedidos[$i]["id"] }}</strong></span>
                                    <div class="card-tools">
                                        <span class="badge badge-info">Novo</span>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2"> 
                                    @for ($x = 0; $x < count($pedidos[$i]["produtos"]); $x++)
                                        <div class="row">
                                            <div class="col-md-2">
                                                {{ $pedidos[$i]["produtos"][$x]["qtd"] }}x
                                            </div>
                                            <div class="col-md-10">
                                                {{ $pedidos[$i]["produtos"][$x]["nome"] }}
                                            </div>
                                        </div> 
                                    @endfor
                                    <hr>
                                    <div>
                                        {{ $pedidos[$i]["novo_endereco"] }}
                                    </div>         
                                    <div>
                                        Valor - R${{ number_format(($pedidos[$i]["total"] - $pedidos[$i]["frete"]), 2, '.', '') }}
                                    </div>         
                                    <div>
                                        Entrega - R${{ $pedidos[$i]["frete"] }}
                                    </div>
                                    <div>
                                        Total - R${{ number_format($pedidos[$i]["total"], 2, '.', '') }}
                                    </div>           
                                    <div>
                                        Forma Pagamento - {{ $pedidos[$i]["forma_pagamento"] }}
                                    </div>
                                    @if ($pedidos[$i]["dinheiro"] != null)
                                        <div>
                                            + R${{ $pedidos[$i]["dinheiro"] }} | Troco: R${{ $pedidos[$i]["troco"] }}
                                        </div> 
                                    @endif          
                                </div>  
                                <div class="card-footer">
                                    <form id="receber" action="{{ route('admin.pedidos.change-status') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="pedido" value="{{ $pedidos[$i]["id"] }}">
                                        <input type="hidden" name="status" value="s">
                                        <input type="hidden" name="cpf" value="{{ auth()->user()->cpf }}">
                                    </form>
                                    <form id="rejeitar" action="{{ route('admin.pedidos.rejeitar') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="pedido" value="{{ $pedidos[$i]["id"] }}">
                                    </form>
                                    <form id="imprimir{{ $pedidos[$i]["id"] }}" action="pedidos/imprimir/{{ $pedidos[$i]["id"] }}" method="get" target="_blank">
                                    </form>
                                    <div class="d-flex justify-content-center">
                                        <button form="rejeitar" type="submit" class="btn btn-danger"><i class="fa-solid fa-square-xmark"></i></button>
                                        <button form="receber" type="submit" class="btn btn-success ml-auto mr-auto"><i class="fa-solid fa-square-check"></i> Receber</button>
                                        <button form="imprimir{{ $pedidos[$i]["id"] }}" type="submit" class="btn btn-info"><i class="fa-solid fa-print"></i></button>
                                    </div>
                                </div>             
                            </div>
                        </div>
                    @endif
                @endfor
            </div>

            <div style="background-color: #efefef; overflow:auto;" class="col-md-3 vh-100">
                @for ($i = 0; $i < count($pedidos); $i++)
                    @if ($pedidos[$i]["status"] == 's')
                        <div class="col-md-12 mt-2">
                            <div class="card collapsed-card">
                                <div class="card-header">
                                    <span>Nº <strong>{{ $pedidos[$i]["id"] }}</strong></span>
                                    <div class="card-tools">
                                        <span class="badge badge-warning text-white">Separação</span>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            <div class="card-body">           
                                @for ($x = 0; $x < count($pedidos[$i]["produtos"]); $x++)
                                        <div class="row">
                                            <div class="col-md-2">
                                                {{ $pedidos[$i]["produtos"][$x]["qtd"] }}x
                                            </div>
                                            <div class="col-md-10">
                                                {{ $pedidos[$i]["produtos"][$x]["nome"] }}
                                            </div>
                                        </div> 
                                    @endfor
                                    <hr>
                                    <div>
                                        {{ $pedidos[$i]["novo_endereco"] }}
                                    </div>         
                                    <div>
                                        Valor - R${{ number_format(($pedidos[$i]["total"] - $pedidos[$i]["frete"]), 2, '.', '') }}
                                    </div>         
                                    <div>
                                        Entrega - R${{ $pedidos[$i]["frete"] }}
                                    </div>
                                    <div>
                                        Total - R${{ number_format($pedidos[$i]["total"], 2, '.', '') }}
                                    </div>           
                                    <div>
                                        Forma Pagamento - {{ $pedidos[$i]["forma_pagamento"] }}
                                    </div>
                                    @if ($pedidos[$i]["dinheiro"] != null)
                                        <div>
                                            + R${{ $pedidos[$i]["dinheiro"] }} | Troco: R${{ $pedidos[$i]["troco"] }}
                                        </div> 
                                    @endif  
                            </div>  
                            <div class="card-footer">
                                    <form action="{{ route('admin.pedidos.change-status') }}" method="post">
                                        @csrf
                                        <div class="d-flex justify-content-center">
                                            <input type="hidden" name="pedido" value="{{ $pedidos[$i]["id"] }}">
                                            <input type="hidden" name="status" value="ac">
                                            <button type="submit" class="btn btn-success">Entregar Pedido</button>
                                        </div>
                                    </form>
                                </div>             
                            </div>
                        </div>
                    @endif
                @endfor
            </div>

            <div style="background-color: #ffffff; overflow:auto;" class="col-md-3 vh-100">
                @for ($i = 0; $i < count($pedidos); $i++)
                    @if ($pedidos[$i]["status"] == 'ac')
                        <div class="col-md-12 mt-2">
                            <div class="card collapsed-card">
                                <div class="card-header">
                                    <span>Nº <strong>{{ $pedidos[$i]["id"] }}</strong></span>
                                    <div class="card-tools">
                                        <span class="badge badge-danger">À caminho</span>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            <div class="card-body">           
                                @for ($x = 0; $x < count($pedidos[$i]["produtos"]); $x++)
                                    <div class="row">
                                        <div class="col-md-2">
                                            {{ $pedidos[$i]["produtos"][$x]["qtd"] }}x
                                        </div>
                                        <div class="col-md-10">
                                            {{ $pedidos[$i]["produtos"][$x]["nome"] }}
                                        </div>
                                    </div> 
                                @endfor
                                <hr>
                                <div>
                                    {{ $pedidos[$i]["novo_endereco"] }}
                                </div>         
                                <div>
                                    Valor - R${{ number_format(($pedidos[$i]["total"] - $pedidos[$i]["frete"]), 2, '.', '') }}
                                </div>         
                                <div>
                                    Entrega - R${{ $pedidos[$i]["frete"] }}
                                </div>
                                <div>
                                    Total - R${{ number_format($pedidos[$i]["total"], 2, '.', '') }}
                                </div>           
                                <div>
                                    Forma Pagamento - {{ $pedidos[$i]["forma_pagamento"] }}
                                </div>
                                @if ($pedidos[$i]["dinheiro"] != null)
                                    <div>
                                        + R${{ $pedidos[$i]["dinheiro"] }} | Troco: R${{ $pedidos[$i]["troco"] }}
                                    </div> 
                                @endif  
                            </div>  
                            <div class="card-footer">
                                    <form action="{{ route('admin.pedidos.change-status') }}" method="post">
                                        @csrf
                                        <div class="d-flex justify-content-center">
                                            <input type="hidden" name="pedido" value="{{ $pedidos[$i]["id"] }}">
                                            <input type="hidden" name="status" value="e">
                                            <button type="submit" class="btn btn-success">Confirmar Entrega</button>
                                        </div>
                                    </form>
                                </div>             
                            </div>
                        </div>
                    @endif
                @endfor
            </div>

            <div style="background-color: #efefef; overflow:auto;" class="col-md-3 vh-100"> 
                @for ($i = 0; $i < count($pedidos); $i++)
                    @if ($pedidos[$i]["status"] == 'e')
                        <div class="col-md-12 mt-2">
                            <div class="card collapsed-card">
                                <div class="card-header">
                                    <span>Nº <strong>{{ $pedidos[$i]["id"] }}</strong></span>
                                    <div class="card-tools">
                                        <span class="badge badge-success">Entregue</span>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">           
                                    @for ($x = 0; $x < count($pedidos[$i]["produtos"]); $x++)
                                        <div class="row">
                                            <div class="col-md-2">
                                                {{ $pedidos[$i]["produtos"][$x]["qtd"] }}x
                                            </div>
                                            <div class="col-md-10">
                                                {{ $pedidos[$i]["produtos"][$x]["nome"] }}
                                            </div>
                                        </div> 
                                    @endfor
                                    <hr>
                                    <div>
                                        {{ $pedidos[$i]["novo_endereco"] }}
                                    </div>         
                                    <div>
                                        Valor - R${{ number_format(($pedidos[$i]["total"] - $pedidos[$i]["frete"]), 2, '.', '') }}
                                    </div>         
                                    <div>
                                        Entrega - R${{ $pedidos[$i]["frete"] }}
                                    </div>
                                    <div>
                                        Total - R${{ number_format($pedidos[$i]["total"], 2, '.', '') }}
                                    </div>           
                                    <div>
                                        Forma Pagamento - {{ $pedidos[$i]["forma_pagamento"] }}
                                    </div>
                                    @if ($pedidos[$i]["dinheiro"] != null)
                                        <div>
                                            + R${{ $pedidos[$i]["dinheiro"] }} | Troco: R${{ $pedidos[$i]["troco"] }}
                                        </div> 
                                    @endif  
                                </div>  
                            </div>
                        </div>
                    @endif
                @endfor 
            </div>
        </div>

    @endisset
@stop

@section('js')
    <script>

        $(document).ready(function() {

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
            },5000);
            $('.close-btn').click(function(){
                $('.alert').removeClass("show");
                $('.alert').addClass("hide");
            });

            
        })

    </script>
@stop