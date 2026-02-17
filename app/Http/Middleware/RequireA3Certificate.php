<?php

namespace App\Http\Middleware;

use App\Services\Security\A3CertificateVerifier;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequireA3Certificate
{
    /**
     * @var A3CertificateVerifier
     */
    private $verifier;

    public function __construct(A3CertificateVerifier $verifier)
    {
        $this->verifier = $verifier;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!config('backup.download_enabled', true)) {
            abort(404);
        }

        $result = $this->verifier->verify($request);
        if (!($result['valid'] ?? false)) {
            Log::warning('A3 certificate validation failed', [
                'reason' => $result['reason'] ?? 'unknown',
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);

            abort(403, 'Certificado A3 valido e obrigatorio para esta operacao.');
        }

        $request->attributes->set('a3_certificate', $result);

        return $next($request);
    }
}
