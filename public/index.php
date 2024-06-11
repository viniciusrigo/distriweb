<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Verifique se o aplicativo está em manutenção
|--------------------------------------------------------------------------
|
| Se a aplicação estiver em modo manutenção/demonstração através do comando "down"
| carregaremos este arquivo para que qualquer conteúdo pré-renderizado possa ser mostrado
| em vez de iniciar o framework, o que poderia causar uma exceção.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Registre o Auto Loader
|--------------------------------------------------------------------------
|
| O Composer fornece um carregador de classes conveniente e gerado automaticamente para
| esta aplicação. Só precisamos utilizá-lo! Nós simplesmente exigiremos isso
| no script aqui para que não precisemos carregar manualmente nossas classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
