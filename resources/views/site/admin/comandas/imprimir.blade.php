<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    
</head>
<body style="width: 80mm; height: auto">
    <div id="corpo">
        <div class="d-flex row justify-content-center">
            <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                <span>Dono da Comanda: <strong>{{ $comanda->nome }}</strong></span>
            </div>
            <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                <span><strong>Produtos Consumidos</strong></span>
            </div>
            <div>
                <table class="table-sm col-12">
                    <tbody>
                        @for ($i = 0; $i < count($comanda->produtos); $i++)     
                        <tr>
                            <td>{{ $comanda->produtos[$i]["nome"] }} {{ $comanda->produtos[$i]["variavel_nome"] }}</td>
                            <td>R${{ $comanda->produtos[$i]["preco"] }}</td>
                        </tr>
                        @endfor
                    </tbody>
                    <tfoot class="border-top">
                        <tr>
                            <td style="text-align: right">Total:</td>
                            <td>R${{$comanda->total}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div style="font-size: 13px" class="d-flex justify-content-center col-12">
                <span>Abertura: <strong>{{ date("H:i:s d/m/Y", strtotime($comanda->data_abertura)) }}</strong></span>
            </div>
        </div>
    </div>
</body>
</html>