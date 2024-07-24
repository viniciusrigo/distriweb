<!DOCTYPE html>
<html lang="pt-br">
 
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>404 Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
        body,
        html {
            height: 100%;
            font-family: "Montserrat", sans-serif;
            font-optical-sizing: auto;
            font-weight: 500;
            font-style: normal; 
        }
    </style>
</head>
 
<body class="d-flex justify-content-cente align-items-center">
    <div class="col-md-12 text-center">
        <h1 style="font-size: 150px">@yield('code')</h1>
        <h2 style="font-size: 60px">@yield('title')</h2>
        <p style="font-size: 20px; font-weight: 300;">@yield('message')</p>
        <a href="{{ route("welcome") }}">Ir para página inicial</a>
    </div>
</body>
 
</html>


