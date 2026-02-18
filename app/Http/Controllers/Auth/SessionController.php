<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthEventService;
use App\Services\Auth\SessionActivityService;
use App\Services\Auth\SessionEventFilters;
use App\Services\Auth\SessionEventsExportService;
use App\Services\Auth\SessionEventsQueryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(
        Request $request,
        SessionActivityService $service,
        SessionEventsQueryService $eventsQuery
    ): View
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $filters = SessionEventFilters::fromRequest($request, 50);
        $events = $eventsQuery->eventsForScreen($user, $filters);

        return view('auth.sessions', [
            'sessions' => $service->listForUser($user),
            'authEvents' => $events,
            'currentSessionId' => (string) session()->getId(),
            'eventFilters' => $filters->toArray(),
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

    public function exportCsv(
        Request $request,
        SessionEventsQueryService $eventsQuery,
        AuthEventService $eventService,
        SessionEventsExportService $exportService
    ): Response
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $filters = SessionEventFilters::fromRequest($request, 200);
        $events = $eventsQuery->rawFilteredEvents($user, $filters);
        $filename = 'auth-events-' . date('Ymd-His') . '.csv';
        $csv = $exportService->buildCsv($events);
        $sha256 = $exportService->computeSha256($csv);

        $eventService->registerCustomEvent($request, $user, 'sessions_export_csv', [
            'row_count' => count($events),
            'export_sha256' => $sha256,
        ]);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'X-Export-SHA256' => $sha256,
        ]);
    }

    public function verifyExportHash(Request $request, SessionEventsQueryService $eventsQuery): Response
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $data = $request->validate([
            'sha256' => 'required|string|size:64|regex:/^[a-fA-F0-9]{64}$/',
        ]);

        $target = strtolower((string) $data['sha256']);
        $matchedEvent = $eventsQuery->findExportHash($user, $target);

        if (!$matchedEvent) {
            return response()->json([
                'verified' => false,
                'message' => 'Hash nao encontrado nos eventos recentes de exportacao.',
            ], 404);
        }

        return response()->json([
            'verified' => true,
            'hash' => $target,
            'event_type' => (string) ($matchedEvent['type'] ?? ''),
            'timestamp' => (string) ($matchedEvent['timestamp'] ?? ''),
            'request_id' => (string) ($matchedEvent['request_id'] ?? ''),
            'row_count' => (int) ($matchedEvent['row_count'] ?? 0),
        ]);
    }

}
