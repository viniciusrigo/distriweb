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
                <span>Dono da Comanda: <strong>{{ $comanda->nome }}</strong></span>
            </div>
            <div>
                <span><strong>Produtos Consumidos</strong></span>
            </div>
            <div>
                <table class="table-sm">
                    <tbody>
                        @for ($i = 0; $i < count($comanda->produtos); $i++)     
                        <tr>
                            <td>{{ $comanda->produtos[$i]["quantidade"] }}x</td>
                            <td>{{ $comanda->produtos[$i]["nome"] }}</td>
                            <td>R${{ $comanda->produtos[$i]["preco"] }}</td>
                        </tr>
                        @endfor
                    </tbody>
                    <tfoot class="border-top">
                        <tr>
                            <td></td>
                            <td></td>
                            <td>R${{$comanda->total}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div>
                <span>Abertura: <strong>{{ date("H:i:s d/m/Y", strtotime($comanda->data_abertura)) }}</strong></span>
            </div>
        </div>
    </div>
</body>
</html>