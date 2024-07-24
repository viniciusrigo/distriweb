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
                    <span>Venda: Manual</span>
                </div>
                <div style="border-top: 2px dashed black;border-bottom: 2px dashed black; font-size: 12px" class="d-flex justify-content-center col-12">
                    <table class="col-11">
                        <tbody>
                            @foreach ($produtos_pdv as $produto)
                                <tr>
                                    <td>{{ $produto['nome'] }} {{ $produto['variavel_nome'] }} </td>
                                    <td>R${{ $produto['promocao'] == "n" ? $produto['preco'] : $produto['preco_promocao'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    Destino: {{ $destino }} - Zona {{ $zona->nome }}
                </div>
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    Entrega: R${{ $zona->entrega }}
                </div>
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    Total Produtos: R${{ $venda['valor'] }}
                </div>
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    Total: R${{ number_format(($venda['valor'] + $zona->entrega), 2, '.', ',') }}
                </div> 
                <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                    Forma Pagamento: {{ $pagamento->nome }}
                </div>
                @if($dinheiro != "")
                    <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                        Dinheiro: R${{ $dinheiro }} - Troco: R${{ $troco }}
                    </div>    
                @endif
            </div>
        </div>
    </div>
</body>
</html>