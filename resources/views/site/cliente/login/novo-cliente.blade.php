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
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');
            body{
                font-family: 'Poppins', sans-serif;
                background: #ececec;
            }
            /*------------ Login container ------------*/
            .box-area{
                width: 930px;
            }
            /*------------ Right box ------------*/
            .right-box{
                padding: 40px 30px 40px 40px;
            }
            /*------------ Custom Placeholder ------------*/
            ::placeholder{
                font-size: 16px;
            }
            .rounded-4{
                border-radius: 20px;
            }
            .rounded-5{
                border-radius: 30px;
            }
            /*------------ For small screens------------*/
            @media only screen and (max-width: 768px){
                .box-area{
                    margin: 0 10px;
                }
                .left-box{
                    height: 100px;
                    overflow: hidden;
                }
                .right-box{
                    padding: 20px;
                }
            }
        </style>

    </head>

    <body class="">
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
           <div class="row border rounded-5 p-3 bg-white shadow box-area">
                <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #913e96;">
                    <div class="featured-image mb-3">
                        <img src="{{ asset('registercliente.png') }}" class="img-fluid" style="width: 250px;">
                    </div>
                    <p class="text-white fs-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Torne-se cliente.</p>
                    <small class="text-white text-wrap text-center" style="width: 17rem;font-family: 'Poppins', sans-serif;">Tenha descontos no <strong>CLUBE FIDELIDADE</strong></small>
                </div>          
                <div class="col-md-6 right-box">
                    <div class="row align-items-center">
                        <div class="header-text mb-1">
                            <h2>Bora ser cliente</h2>
                        </div>
                        <form action="{{ route('novo.cliente.register') }}" method="post">
                            @csrf
                            <div class="form-row d-flex flex-wrap">                            
                                <div class="form-group col-md-12 mb-1">
                                    <input type="text" class="form-control form-control-sm fs-6" name="name" placeholder="Nome">
                                </div>
                                <div class="form-group col-md-12 mb-1">
                                    <input type="text" class="form-control form-control-sm fs-6" name="email" placeholder="E-Mail">
                                </div>
                                <div class="form-group col-md-6 mb-1">
                                    <input type="text" class="form-control form-control-sm fs-6" name="cpf" placeholder="CPF">
                                </div>
                                <div class="form-group col-md-6 mb-1">
                                    <input type="text" class="form-control form-control-sm fs-6" name="celular" placeholder="EX: 43988887777">
                                </div>
                                <div class="form-group col-md-6 mb-1">
                                    <input type="text" class="form-control form-control-sm fs-6" id="cep" name="cep" placeholder="CEP">
                                </div>
                                <div class="form-group col-md-6 mb-1">
                                    <select class="form-control form-control-sm fs-6" name="zona_id">
                                        <option value="">Zona...</option>
                                        <option value="1">Norte</option>
                                        <option value="2">Sul</option>
                                        <option value="3">Leste</option>
                                        <option value="4">Oeste</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12 mb-1">
                                    <input type="text" class="form-control form-control-sm fs-6" id="logradouro" name="logradouro" placeholder="Logradouro">
                                </div>
                                <div class="form-group col-md-3 mb-1">
                                    <input type="text" class="form-control form-control-sm fs-6" name="numero" placeholder="Nº">
                                </div>
                                <div class="form-group col-md-9 mb-1">
                                    <input type="text" class="form-control form-control-sm fs-6" name="complemento" placeholder="Complemento">
                                </div>
                                <input type="hidden" id="bairro" name="bairro" value="">
                                <input type="hidden" id="localidade" name="localidade" value="">
                                <input type="hidden" id="uf" name="uf" value="">
                                <input type="hidden" id="ibge" name="ibge" value="">
                                <input type="hidden" id="ddd" name="ddd" value="">
                                
                                <div class="form-group col-md-6 mb-1">
                                    <input type="password" class="form-control form-control-sm fs-6" name="password" placeholder="Senha">
                                </div>
                                <div class="form-group col-md-6 mb-1">
                                    <input type="password" class="form-control form-control-sm fs-6" placeholder="Confirmar Senha">
                                </div>

                                <div class="input-group mb-3">
                                    <button class="btn btn-lg w-100 fs-6 text-white" style="background-color: #913e96">Cadastrar</button>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <small>Já é cadastrado? <a href="{{ route('logincliente') }}">Faça o login</a></small>
                        </div>
                    </div>
                </div> 
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>   
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script>
            $('#cep').on('blur', function() {
                var busca = this.value;
                $.get('https://viacep.com.br/ws/'+busca+'/json/', function (dados){
                    $('#logradouro').val(dados.logradouro)
                    $('#bairro').val(dados.bairro)
                    $('#localidade').val(dados.localidade)
                    $('#uf').val(dados.uf)
                    $('#ibge').val(dados.ibge)
                    $('#ddd').val(dados.ddd)
                })  
            })
        </script>
    </body>

</html>
