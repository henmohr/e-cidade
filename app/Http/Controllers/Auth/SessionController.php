<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthEventService;
use App\Services\Auth\SessionActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(SessionActivityService $service, AuthEventService $eventService): View
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        return view('auth.sessions', [
            'sessions' => $service->listForUser($user),
            'authEvents' => $eventService->listRecentEventsForUser($user),
            'currentSessionId' => (string) session()->getId(),
        ]);
    }

    public function revoke(Request $request, SessionActivityService $service): RedirectResponse
    {
        $request->validate([
            'session_id' => 'required|string|max:200',
        ]);

        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $sessionId = (string) $request->input('session_id');
        $ok = $service->revokeSession($user, $sessionId);
        if (!$ok) {
            return back()->withErrors(['session_id' => 'Sessao nao encontrada ou ja encerrada.']);
        }

        Log::info('User session revoked', [
            'user_id' => $user->getAuthIdentifier(),
            'session_id' => $sessionId,
            'actor_session_id' => (string) session()->getId(),
            'ip' => $request->ip(),
        ]);

        return back()->with('status', 'Sessao encerrada com sucesso.');
    }
}
