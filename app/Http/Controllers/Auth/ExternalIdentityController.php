<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthEventService;
use App\Services\Auth\AuthEventTypes;
use App\Services\Auth\ExternalIdentityService;
use App\Services\Auth\SessionActivityService;
use App\Support\Session\LegacySession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExternalIdentityController extends Controller
{
    public function callback(
        Request $request,
        ExternalIdentityService $service,
        SessionActivityService $sessionService
    ): RedirectResponse|JsonResponse
    {
        if (!$service->isEnabled()) {
            return $this->deny($request, 404, 'Integracao de identidade externa desabilitada.', [
                'provider' => (string) $request->input('provider', ''),
                'reason' => 'disabled',
            ]);
        }

        $provider = strtolower(trim((string) $request->input('provider', '')));
        if (!$service->isProviderAllowed($provider)) {
            return $this->deny($request, 422, 'Provedor externo nao permitido.', [
                'provider' => $provider,
                'reason' => 'provider_not_allowed',
            ]);
        }

        $rawPayload = (string) $request->input('payload', '');
        if ($rawPayload === '') {
            $payloadArray = $request->only(['provider', 'subject', 'cpf', 'login', 'email', 'name', 'expires_at']);
            $payloadArray['provider'] = $provider;
            $rawPayload = json_encode($payloadArray) ?: '{}';
        }

        $signature = (string) $request->header('X-Identity-Signature', (string) $request->input('signature', ''));
        if (!$service->verifySignature($provider, $rawPayload, $signature)) {
            return $this->deny($request, 403, 'Assinatura invalida para callback externo.', [
                'provider' => $provider,
                'reason' => 'invalid_signature',
            ]);
        }

        $claims = json_decode($rawPayload, true);
        if (!is_array($claims)) {
            return $this->deny($request, 422, 'Payload de identidade invalido.', [
                'provider' => $provider,
                'reason' => 'invalid_payload',
            ]);
        }

        if (!$service->validateClaimsWindow($claims)) {
            return $this->deny($request, 401, 'Claims expirados ou invalidos para login externo.', [
                'provider' => $provider,
                'reason' => 'expired_claims',
            ]);
        }

        if (!$service->consumeNonce($claims)) {
            return $this->deny($request, 409, 'Nonce invalido ou ja utilizado.', [
                'provider' => $provider,
                'reason' => 'nonce_reused_or_missing',
            ]);
        }

        $user = $service->resolveUser($claims);
        if (!$user) {
            return $this->deny($request, 401, 'Usuario nao encontrado para o identificador recebido.', [
                'provider' => $provider,
                'reason' => 'user_not_found',
                'identifier_hint' => $this->identifierHint($claims),
            ]);
        }

        session()->put(LegacySession::DB_ID_USUARIO, (int) $user->getAuthIdentifier());
        session()->put(LegacySession::DB_LOGIN, (string) ($user->login ?? ''));
        session()->put(LegacySession::DB_IP, (string) $request->ip());
        session()->put(LegacySession::DB_DATAUSU, date('Y-m-d'));
        session()->put(LegacySession::DB_UOL_HORA, time());
        session()->put(LegacySession::DB_ANOUSU, (int) date('Y'));
        session()->put(LegacySession::DB_INSTIT, (int) config('external_identity.default_instit', 1));
        session()->put('auth.external.provider', $provider);

        Auth::loginUsingId((int) $user->getAuthIdentifier());
        $request->session()->regenerate();
        $sessionService->touch($user, $request);
        $revokedCount = $sessionService->revokeOtherSessions($user, (string) $request->session()->getId());

        app(AuthEventService::class)->registerExternalSuccess($request, $user, $provider);
        if ($revokedCount > 0) {
            app(AuthEventService::class)->registerCustomEvent($request, $user, AuthEventTypes::SESSION_REVOKE_OTHERS, [
                'revoked_count' => $revokedCount,
            ]);
        }

        Log::info('External identity login succeeded', [
            'provider' => $provider,
            'user_id' => $user->getAuthIdentifier(),
            'login' => $user->login,
            'ip' => $request->ip(),
            'revoked_sessions' => $revokedCount,
        ]);

        $redirectPath = (string) config('external_identity.redirect_path', '/web/welcome');
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login externo realizado com sucesso.',
                'redirect' => $redirectPath,
            ]);
        }

        return redirect()->to($redirectPath);
    }

    public function providers(ExternalIdentityService $service): JsonResponse
    {
        if (!$service->isEnabled()) {
            return response()->json(['enabled' => false, 'providers' => []], 200);
        }

        return response()->json([
            'enabled' => true,
            'providers' => $service->allowedProviders(),
        ]);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function deny(Request $request, int $status, string $message, array $context = []): RedirectResponse|JsonResponse
    {
        $payload = array_merge([
            'status' => $status,
            'message' => $message,
            'ip' => $request->ip(),
            'path' => '/' . ltrim($request->path(), '/'),
            'method' => $request->method(),
        ], $context);

        Log::warning('External identity login denied', $payload);
        Log::channel((string) config('web_audit.channel', 'web_audit'))
            ->warning('External identity callback denied', $payload);

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return redirect()->to('/')->withErrors(['auth_external' => $message]);
    }

    /**
     * @param array<string, mixed> $claims
     */
    private function identifierHint(array $claims): string
    {
        $cpf = preg_replace('/\D+/', '', (string) ($claims['cpf'] ?? ''));
        if ($cpf !== '') {
            return 'cpf:' . substr($cpf, -4);
        }

        $login = trim((string) ($claims['login'] ?? ''));
        if ($login !== '') {
            return 'login:' . substr($login, 0, 3) . '***';
        }

        return 'none';
    }
}
