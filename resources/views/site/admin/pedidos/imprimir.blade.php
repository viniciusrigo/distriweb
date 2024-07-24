<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <style>
        @page { size: 80mm 80mm }
        @media print {
            body {
                width: 80mm
            } 
        } 
    </style>
</head>
<body style="width: 80mm; height: auto">
    <div>
        <div>
            <div class="d-flex row justify-content-center">
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    <span><strong>CNPJ: {{ $info_empresa["cnpj"] }} - {{ $info_empresa["nome_fantasia"] }}</strong></span>
                </div>
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                <span>{{ $info_empresa["logradouro"] }}, {{ $info_empresa["numero"] }} - {{ $info_empresa["bairro"] }}</span>
                </div>  
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    <span>Contato: {{ $info_empresa["telefone"] }}</span>
                </div>  
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    <span>Venda: Via Internet</span>
                </div>
                <div style="border-top: 2px dashed black;border-bottom: 2px dashed black; font-size: 13px" class="d-flex justify-content-center col-12">
                    <table class="col-11">
                        <thead>
                            <tr>
                                <th>QTD</th>
                                <th>Produto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < count($pedido['produtos']); $i++)
                            <tr>
                                <td>1x</td>
                                <td>{{ $pedido['produtos'][$i]['nome'] }} {{ $pedido['produtos'][$i]['variavel_nome'] }}</td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    Destino: {{ $pedido['novo_endereco'] }}
                </div>
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    Valor: R${{ number_format(($pedido['total'] - $pedido['frete'] ), 2, '.', '') }} - Entrega: R${{ $pedido['frete'] }}
                </div>
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    Total: R${{ $pedido['total']}}
                </div>
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    Forma Pagamento: {{ $pedido['forma_pagamento'] }}
                </div>
                @if ($pedido['dinheiro'] != null)
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    + R${{ $pedido["dinheiro"] }} | Troco: R${{ $pedido["troco"] }}
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>