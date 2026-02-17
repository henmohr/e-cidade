<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

        if (!$mfaService->verifyForUser($user, $request->input('code'))) {
            return back()->withErrors(['code' => 'Código MFA inválido ou expirado.']);
        }

        return redirect()->to('/web/welcome');
    }

    public function resend(MfaService $mfaService): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $mfaService->issueForUser($user, true);
        return back()->with('status', 'Novo código MFA enviado.');
    }
}

