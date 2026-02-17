<?php

namespace App\Http\Middleware;

use App\Services\Auth\MfaService;
use App\Support\Session\LegacySession;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthEcidadeUser
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $loggedUserId = session(LegacySession::DB_ID_USUARIO);

        if(empty($loggedUserId)) {
            abort(401);
        }

        Auth::loginUsingId($loggedUserId);

        $user = Auth::user();
        if (empty($user)) {
            abort(401);
        }

        /** @var MfaService $mfaService */
        $mfaService = app(MfaService::class);
        if ($mfaService->requiresMfa($user)) {

            if ($request->routeIs('mfa.*')) {
                return $next($request);
            }

            if (!$mfaService->isVerified($user)) {
                $mfaService->issueForUser($user);

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'MFA obrigatório para este usuário.'
                    ], 403);
                }

                return redirect()->route('mfa.challenge');
            }
        }

        return $next($request);
    }
}
