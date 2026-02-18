<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\AuthEventService;
use App\Services\Auth\AuthEventMetaKeys;
use App\Services\Auth\AuthMessages;
use App\Services\Auth\AuthEventTypes;
use App\Services\Auth\ExportHash;
use App\Services\Auth\SessionExportEvidencePresenter;
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
    private const EXPORT_FILE_PREFIX = 'auth-events-';
    private const EXPORT_FILE_EXTENSION = '.csv';
    private const EXPORT_CONTENT_TYPE = 'text/csv; charset=UTF-8';
    private const HEADER_CONTENT_TYPE = 'Content-Type';
    private const HEADER_CONTENT_DISPOSITION = 'Content-Disposition';
    private const HEADER_EXPORT_SHA256 = 'X-Export-SHA256';

    public function index(
        Request $request,
        SessionActivityService $service,
        SessionEventsQueryService $eventsQuery
    ): View
    {
        $user = $this->authenticatedUserOrAbort();

        $filters = SessionEventFilters::fromScreenRequest($request);
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

        $user = $this->authenticatedUserOrAbort();

        $sessionId = (string) $request->input('session_id');
        $ok = $service->revokeSession($user, $sessionId);
        if (!$ok) {
            return back()->withErrors(['session_id' => AuthMessages::SESSION_NOT_FOUND]);
        }

        Log::info('User session revoked', [
            'user_id' => $user->getAuthIdentifier(),
            'session_id' => $sessionId,
            'actor_session_id' => (string) session()->getId(),
            'ip' => $request->ip(),
        ]);
        app(AuthEventService::class)->registerCustomEvent($request, $user, AuthEventTypes::SESSION_REVOKED, [
            AuthEventMetaKeys::TARGET_SESSION_ID => $sessionId,
        ]);

        return back()->with('status', AuthMessages::SESSION_REVOKED_SUCCESS);
    }

    public function exportCsv(
        Request $request,
        SessionEventsQueryService $eventsQuery,
        AuthEventService $eventService,
        SessionEventsExportService $exportService
    ): Response
    {
        $user = $this->authenticatedUserOrAbort();

        $filters = SessionEventFilters::fromExportRequest($request);
        $events = $eventsQuery->rawFilteredEvents($user, $filters);
        $filename = $this->exportFilename();
        $csv = $exportService->buildCsv($events);
        $sha256 = $exportService->computeSha256($csv);

        $eventService->registerCustomEvent($request, $user, AuthEventTypes::SESSIONS_EXPORT_CSV, [
            AuthEventMetaKeys::ROW_COUNT => count($events),
            AuthEventMetaKeys::EXPORT_SHA256 => $sha256,
        ]);

        return response($csv, 200, [
            self::HEADER_CONTENT_TYPE => self::EXPORT_CONTENT_TYPE,
            self::HEADER_CONTENT_DISPOSITION => 'attachment; filename="' . $filename . '"',
            self::HEADER_EXPORT_SHA256 => $sha256,
        ]);
    }

    public function verifyExportHash(
        Request $request,
        SessionEventsQueryService $eventsQuery,
        SessionExportEvidencePresenter $presenter
    ): Response
    {
        $user = $this->authenticatedUserOrAbort();

        $data = $request->validate([
            'sha256' => 'required|string|size:' . ExportHash::LENGTH . '|' . ExportHash::VALIDATION_RULE,
        ]);

        $target = ExportHash::normalize((string) $data['sha256']);
        $matchedEvent = $eventsQuery->findExportHash($user, $target);

        if (!$matchedEvent) {
            return response()->json($presenter->notFound(), 404);
        }

        return response()->json($presenter->verified($target, $matchedEvent));
    }

    private function authenticatedUserOrAbort(): User
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            abort(401);
        }

        return $user;
    }

    private function exportFilename(): string
    {
        return self::EXPORT_FILE_PREFIX . date('Ymd-His') . self::EXPORT_FILE_EXTENSION;
    }
}
