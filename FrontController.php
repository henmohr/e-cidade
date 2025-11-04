<?php

try {

    // API v2 is handled by Laravel (modern routes)
    if (preg_match('/[^?]*api\/v2\//' , $_SERVER['REQUEST_URI'])) {
        return require_once 'public/index.php';
    }

    // API v1 is handled by Silex (legacy)
    if (preg_match('/[^?]*api\/v1\//' , $_SERVER['REQUEST_URI'])) {
        return require_once 'api/api.php';
    }

    if (preg_match('/[^?]*\/api/', $_SERVER['REQUEST_URI'])) {
        return require_once 'public/index.php';
    }

    if (preg_match('/[^?]*\/ui-blade/', $_SERVER['REQUEST_URI'])) {
        return require_once 'public/index.php';
    }

    return require('app.php');
} catch (Exception $e) {
    if (config('app.debug')) {
        throw $e;
    }
    //@todo - verificar a possíbilidade de enviar o erro para o monitoramento
    logger()->emergency($e->getMessage(), $e->getTrace());
}

