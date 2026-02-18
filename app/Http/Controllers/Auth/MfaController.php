<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthEventService;
use App\Services\Auth\AuthEventTypes;
use App\Services\Auth\MfaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MfaController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        return view('auth.mfa-challenge');
    }

    public function verify(Request $request, MfaService $mfaService): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:8',
        ]);

        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $blockSeconds = $mfaService->currentBlockSecondsForUser($user);
        if ($blockSeconds > 0) {
            app(AuthEventService::class)->registerCustomEvent($request, $user, AuthEventTypes::MFA_VERIFY_BLOCKED, [
                'blocked_seconds' => $blockSeconds,
            ]);
            return back()->withErrors([
                'code' => sprintf('MFA temporariamente bloqueado. Tente novamente em %d segundos.', $blockSeconds),
            ]);
        }

        if (!$mfaService->verifyForUser($user, $request->input('code'))) {
            app(AuthEventService::class)->registerCustomEvent($request, $user, AuthEventTypes::MFA_VERIFY_FAILED);
            return back()->withErrors(['code' => 'Código MFA inválido ou expirado.']);
        }

        app(AuthEventService::class)->registerCustomEvent($request, $user, AuthEventTypes::MFA_VERIFY_SUCCESS);
        return redirect()->to('/web/welcome');
    }

    public function resend(Request $request, MfaService $mfaService): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $blockSeconds = $mfaService->currentBlockSecondsForUser($user);
        if ($blockSeconds > 0) {
            return back()->withErrors([
                'code' => sprintf('MFA temporariamente bloqueado. Aguarde %d segundos para reenviar.', $blockSeconds),
            ]);
        }

        $mfaService->issueForUser($user, true);
        app(AuthEventService::class)->registerCustomEvent($request, $user, AuthEventTypes::MFA_CODE_RESENT);
        return back()->with('status', 'Novo código MFA enviado.');
    }
}
