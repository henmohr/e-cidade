<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Guards do Ciclo da Despesa
    |--------------------------------------------------------------------------
    |
    | Quando habilitado, ativa validacoes de sequencia em pontos modernos de
    | gravacao (liquidacao e pagamento) para evitar salto de etapa.
    |
    */
    'enforce_model_guards' => env('EXECUCAO_ORCAMENTARIA_ENFORCE_GUARDS', true),
];
