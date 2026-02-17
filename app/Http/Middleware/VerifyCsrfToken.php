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
        '/web/iptufoto/*',
        '/web/datagrid/*',
        '/web/patrimonial/licitacoes/procedimentos/julgamento-por-lance/*',
        '/web/redesim/*',
        '/web/patrimonial/*',
        'web/configuracao/configuracao/procedimentos/manutencao-de-dados/manutencao-lancamentos-patrimonial/controle-de-datas/*',
        'web/idp/callback',
    ];
}
