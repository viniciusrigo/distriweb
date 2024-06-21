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
            <div>
                <span>Padovani Bebidas</span>
            </div>
            <div>
                <span>Rua lateral, 123 - JD Padovani</span>
            </div>
            <div>
                <span>Contato: (43) 93344-5566</span>
            </div>
            <div>
                <span>CNPJ: 12.345.678/0001-10</span>
            </div>  
            <div>
                <span>Venda: Via Internet</span>
            </div> 
            <hr>
            @for ($i = 0; $i < count($pedido['produtos']); $i++)
                <div>{{ strval($pedido['produtos'][$i]['qtd']."x  |  ".$pedido['produtos'][$i]['nome']) }}</div>
            @endfor
            <hr>
            <div>Destino: {{ $pedido['novo_endereco'] }}</div>
            <div>Valor: R${{ number_format(($pedido['total'] - $pedido['frete'] ), 2, '.', '') }}</div>
            <div>Entrega: R${{ $pedido['frete'] }}</div>
            <div>Total: R${{ $pedido['total']}}</div>
            <div>Forma Pagamento: {{ $pedido['forma_pagamento'] }}</div>
            @if ($pedido['dinheiro'] != null)
                <div>
                    + R${{ $pedido["dinheiro"] }} | Troco: R${{ $pedido["troco"] }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>