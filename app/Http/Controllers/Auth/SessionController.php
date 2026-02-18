<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthEventService;
use App\Services\Auth\AuthEventPresenter;
use App\Services\Auth\SessionActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(
        Request $request,
        SessionActivityService $service,
        AuthEventService $eventService,
        AuthEventPresenter $presenter
    ): View
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $eventType = trim((string) $request->query('event_type', ''));
        $eventRequestId = trim((string) $request->query('event_request_id', ''));
        $eventLimit = (int) $request->query('event_limit', 50);

        $events = array_map(function (array $event) use ($presenter) {
            $event['type_label'] = $presenter->typeLabel($event);
            $event['details'] = $presenter->details($event);
            return $event;
        }, $eventService->listRecentEventsForUserFiltered($user, $eventType, $eventRequestId, $eventLimit));

        return view('auth.sessions', [
            'sessions' => $service->listForUser($user),
            'authEvents' => $events,
            'currentSessionId' => (string) session()->getId(),
            'eventFilters' => [
                'event_type' => $eventType,
                'event_request_id' => $eventRequestId,
                'event_limit' => $eventLimit > 0 ? $eventLimit : 50,
            ],
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
        app(AuthEventService::class)->registerCustomEvent($request, $user, 'session_revoked', [
            'target_session_id' => $sessionId,
        ]);

        return back()->with('status', 'Sessao encerrada com sucesso.');
    }
}
