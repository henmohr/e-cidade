<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthEventMetaKeys;
use App\Services\Auth\AuthEventService;
use App\Services\Auth\AuthMessages;
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
                AuthEventMetaKeys::BLOCKED_SECONDS => $blockSeconds,
            ]);
            return back()->withErrors([
                'code' => AuthMessages::mfaBlockedTryAgain($blockSeconds),
            ]);
        }

        if (!$mfaService->verifyForUser($user, $request->input('code'))) {
            app(AuthEventService::class)->registerCustomEvent($request, $user, AuthEventTypes::MFA_VERIFY_FAILED);
            return back()->withErrors(['code' => AuthMessages::MFA_INVALID_OR_EXPIRED]);
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
                'code' => AuthMessages::mfaBlockedResend($blockSeconds),
            ]);
        }

        $mfaService->issueForUser($user, true);
        app(AuthEventService::class)->registerCustomEvent($request, $user, AuthEventTypes::MFA_CODE_RESENT);
        return back()->with('status', AuthMessages::MFA_RESEND_SUCCESS);
    }
}
