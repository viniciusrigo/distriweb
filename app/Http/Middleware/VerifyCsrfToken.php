<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'https://distriweb.com.br/admin/indicadores-ajax',
        'https://distriweb.com.br/admin/dados',
        'https://distriweb.com.br/admin/comandas/novo-pedido',
    ];
}
