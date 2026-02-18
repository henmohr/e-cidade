<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthEventService;
use App\Services\Auth\AuthEventPresenter;
use App\Services\Auth\SessionActivityService;
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

    public function exportCsv(Request $request, AuthEventService $eventService): Response
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $eventType = trim((string) $request->query('event_type', ''));
        $eventRequestId = trim((string) $request->query('event_request_id', ''));
        $eventLimit = (int) $request->query('event_limit', 200);

        $events = $eventService->listRecentEventsForUserFiltered($user, $eventType, $eventRequestId, $eventLimit);
        $filename = 'auth-events-' . date('Ymd-His') . '.csv';
        $csv = $this->buildCsv($events);
        $sha256 = hash('sha256', $csv);

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

    /**
     * @param array<string, mixed> $event
     */
    private function detailsCsvColumn(array $event): string
    {
        $parts = [];

        if (isset($event['revoked_count'])) {
            $parts[] = 'revoked_count=' . (int) $event['revoked_count'];
        }

        if (!empty($event['target_session_id'])) {
            $parts[] = 'target_session_id=' . (string) $event['target_session_id'];
        }

        if (!empty($event['tier'])) {
            $parts[] = 'tier=' . (string) $event['tier'];
        }

        if (!empty($event['file'])) {
            $parts[] = 'file=' . (string) $event['file'];
        }

        if (isset($event['blocked_seconds'])) {
            $parts[] = 'blocked_seconds=' . (int) $event['blocked_seconds'];
        }

        return implode(';', $parts);
    }

    /**
     * @param array<int, array<string, mixed>> $events
     */
    private function buildCsv(array $events): string
    {
        $out = fopen('php://temp', 'r+');
        fputcsv($out, ['type', 'request_id', 'timestamp', 'ip', 'provider', 'details']);

        foreach ($events as $event) {
            fputcsv($out, [
                (string) ($event['type'] ?? ''),
                (string) ($event['request_id'] ?? ''),
                (string) ($event['timestamp'] ?? ''),
                (string) ($event['ip'] ?? ''),
                (string) ($event['provider'] ?? ''),
                $this->detailsCsvColumn($event),
            ]);
        }

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return is_string($csv) ? $csv : '';
    }
}
