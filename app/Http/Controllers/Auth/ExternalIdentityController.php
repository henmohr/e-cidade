<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthEventService;
use App\Services\Auth\ExternalIdentityService;
use App\Support\Session\LegacySession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExternalIdentityController extends Controller
{
    public function callback(Request $request, ExternalIdentityService $service): RedirectResponse|JsonResponse
    {
        if (!$service->isEnabled()) {
            return $this->deny($request, 404, 'Integracao de identidade externa desabilitada.');
        }

        $provider = strtolower(trim((string) $request->input('provider', '')));
        if (!$service->isProviderAllowed($provider)) {
            return $this->deny($request, 422, 'Provedor externo nao permitido.');
        }

        $rawPayload = (string) $request->input('payload', '');
        if ($rawPayload === '') {
            $payloadArray = $request->only(['provider', 'subject', 'cpf', 'login', 'email', 'name', 'expires_at']);
            $payloadArray['provider'] = $provider;
            $rawPayload = json_encode($payloadArray) ?: '{}';
        }

        $signature = (string) $request->header('X-Identity-Signature', (string) $request->input('signature', ''));
        if (!$service->verifySignature($provider, $rawPayload, $signature)) {
            return $this->deny($request, 403, 'Assinatura invalida para callback externo.');
        }

        $claims = json_decode($rawPayload, true);
        if (!is_array($claims)) {
            return $this->deny($request, 422, 'Payload de identidade invalido.');
        }

        if (!$service->validateClaimsWindow($claims)) {
            return $this->deny($request, 401, 'Claims expirados ou invalidos para login externo.');
        }

        if (!$service->consumeNonce($claims)) {
            return $this->deny($request, 409, 'Nonce invalido ou ja utilizado.');
        }

        $user = $service->resolveUser($claims);
        if (!$user) {
            return $this->deny($request, 401, 'Usuario nao encontrado para o identificador recebido.');
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
        app(AuthEventService::class)->registerExternalSuccess($request, $user, $provider);

        Log::info('External identity login succeeded', [
            'provider' => $provider,
            'user_id' => $user->getAuthIdentifier(),
            'login' => $user->login,
            'ip' => $request->ip(),
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

    private function deny(Request $request, int $status, string $message): RedirectResponse|JsonResponse
    {
        Log::warning('External identity login denied', [
            'status' => $status,
            'message' => $message,
            'ip' => $request->ip(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return redirect()->to('/')->withErrors(['auth_external' => $message]);
    }
}
