@extends('adminlte::page')

@section('title', 'DW - Bancos')

@section('css')
    <style>
        .escolha div {
            display: flex;
            flex-wrap: wrap;
            margin-top: 0.5rem;
            justify-content: center;
        }

        .escolha input[type="radio"] {
            clip: rect(0 0 0 0);
            clip-path: inset(100%);
            height: 1px;
            overflow: hidden;
            position: absolute;
            white-space: nowrap;
            width: 1px;
        }

        .escolha input[type="radio"]:checked + span {
            box-shadow: 0 0 0 0.0625em #0043ed;
            background-color: #dee7ff;
            z-index: 1;
            color: #0043ed;
        }

        label span {
            display: block;
            cursor: pointer;
            background-color: #fff;
            padding: 0.375em .75em;
            position: relative;
            margin-left: .0625em;
            box-shadow: 0 0 0 0.0625em #b5bfd9;
            letter-spacing: .05em;
            color: #3e4963;
            text-align: center;
            transition: background-color .5s ease;
        }

        label:first-child span {
            border-radius: .375em 0 0 .375em;
        }

        label:last-child span {
            border-radius: 0 .375em .375em 0;
        }
    </style>
@stop

@section('content')
    <div class="d-flex justify-content-center align-items-center row vh-100">
        <div class="d-flex row col-12 justify-content-center">
            <button class="btn btn-primary rounded-pill" data-toggle="modal" data-target="#mov-extra"><i class="fa-solid fa-plus"></i> Movimentação Extraordinária</button>
            <div class="modal fade" id="mov-extra" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>
                            <div class="modal-body">
                                <form id="form-mov-extra" class="d-flex justify-content-center row" action="{{ route('admin.banco.mov-extra') }}" method="POST">
                                    @csrf
                                    <div class="d-flex justify-content-center form-group col-md-12" style="padding: 3px;">
                                        <div class="escolha">
                                            <div>
                                                <label>
                                                    <input type="radio" name="acao" value="Remover" checked="">
                                                    <span>Remover</span>
                                                </label>
                                                <label>
                                                    <input type="radio" name="acao" value="Adicionar">
                                                    <span>Adicionar</span>
                                                </label>                                  
                                            </div>
                                        </div>
                                    </div>
                                    <input class="form-control col-5" type="text" name="valor" placeholder="Valor R$" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 13 || event.charCode == 44" maxlength="7" required>
                                    <select class="form-control col-5" name="banco_id" required >
                                        <option value="">Escolhe o Banco...</option>
                                        @foreach ($bancos as $banco)   
                                            <option value="{{ $banco->id }}">{{ $banco->nome }}</option>
                                        @endforeach
                                    </select>
                                    <input class="form-control col-10" type="text" name="motivo" placeholder="Descreva o motivo..." maxlength="30" required>
                                </form>
                            </div>
                            <div class="modal-footer">
                            <button form="form-mov-extra" type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex row col-12 justify-content-center">
            @foreach ($bancos as $banco)  
                <div style="height: 80vh" class="d-flex flex-wrap col-3 justify-content-center ml-3">
                    <div style="box-shadow: 0px 5px 20px #888888; height:400px; overflow:auto; border-radius: 10px" class="bg-white col-md-12 col-sm-4 col-10 m-2">
                        <table class="col-12" id="tabela" class="table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Valor</th>
                                    <th>Tipo</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < count($banco->fluxos); $i++)
                                    <tr class="tb-tr-bd">
                                        <td style="text-align:left">
                                            R${{ $banco->fluxos[$i]->valor }} 
                                            @if ($banco->fluxos[$i]->mov_extra == "s")
                                                <i class='fa-regular fa-circle-question' title="{{ $banco->fluxos[$i]->motivo }} "></i>
                                            @endif
                                        </td>
                                        @php                                                
                                            if ($banco->fluxos[$i]->tipo == "e") {
                                                echo "<td><span style='background-color: #a4ee92; color: #255a1e; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Entrada</strong></span></td>";
                                            } else {
                                                echo "<td><span style='background-color: #ee9292; color: #DC3545; padding: 0px 8px 0px 8px; border-radius: 10px;'><strong>Saída</strong></span></td>";
                                            }
                                        @endphp
                                        <td style="text-align:left">@php echo date("H:i:s d/m/y", strtotime($banco->fluxos[$i]->data)) @endphp</td>
                                    </tr>
                                @endfor    
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div style="box-shadow: 0px 5px 20px #888888;height: 120px" class="small-box bg-success col-md-12 col-sm-4 col-10 m-2">
                        <div class="inner">
                            <h3>R${{ $banco->saldo }}</h3>
                            <p>{{ $banco->nome }}</p>
                        </div>
                        <div class="icon">
                            <i class="fa-solid fa-piggy-bank"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function(){
            $('.alert').addClass("show");
            $('.alert').removeClass("hide");
            $('.alert').addClass("showAlert");
            setTimeout(function(){
                $('.alert').removeClass("show");
                $('.alert').addClass("hide");
            },3500)
        })
    </script>
@stop