<?php

namespace App\Http\Middleware;

use App\Services\Auth\WebAuditTrailService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebAuditTrailMiddleware
{
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var WebAuditTrailService $service */
        $service = app(WebAuditTrailService::class);
        $startedAt = microtime(true);

        $response = $next($request);

        if (!$service->isEnabled()) {
            return $response;
        }

        if ($service->shouldSkipPath($request->path())) {
            return $response;
        }

        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
        $statusCode = (int) $response->getStatusCode();
        $channel = (string) config('web_audit.channel', 'web_audit');

        Log::channel($channel)->info('Web audit trail', $service->buildContext($request, $statusCode, $durationMs));

        return $response;
    }
}
