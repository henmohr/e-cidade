<?php

namespace App\Http\Middleware;

use App\Services\Auth\AccessPolicyService;
use App\Services\Auth\MfaService;
use App\Services\Auth\SessionActivityService;
use App\Support\Session\LegacySession;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $currentSessionId = (string) $request->session()->getId();

        $user = Auth::user();
        if (empty($user)) {
            abort(401);
        }

        /** @var SessionActivityService $sessionService */
        $sessionService = app(SessionActivityService::class);
        if ($sessionService->isRevoked($currentSessionId)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            abort(401);
        }

        $sessionService->touch($user, $request);

        /** @var AccessPolicyService $accessPolicy */
        $accessPolicy = app(AccessPolicyService::class);
        $policyCheck = $accessPolicy->evaluate($user);
        if (!$policyCheck['allowed']) {
            Log::warning('Access denied by access policy', [
                'user_id' => $user->getAuthIdentifier(),
                'login' => $user->login ?? null,
                'ip' => $request->ip(),
                'reason' => $policyCheck['reason'] ?? 'unknown',
                'detail' => $policyCheck['detail'] ?? '',
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => (string) config('auth_access.deny_message', 'Acesso bloqueado pela politica de horario/perfil.')
                ], 403);
            }

            return redirect()->route('login')
                ->withErrors(['auth' => (string) config('auth_access.deny_message', 'Acesso bloqueado pela politica de horario/perfil.')]);
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
