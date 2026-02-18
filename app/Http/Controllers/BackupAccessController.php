<?php

namespace App\Http\Controllers;

use App\Services\Auth\AuthEventService;
use App\Services\Auth\AuthMessages;
use App\Services\Auth\AuthEventTypes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupAccessController extends Controller
{
    public function index(): View
    {
        $files = [
            'active' => $this->listTier('active'),
            'archive' => $this->listTier('archive'),
        ];

        return view('backup.index', [
            'files' => $files,
            'downloadEnabled' => (bool) config('backup.download_enabled', true),
            'a3Required' => (bool) config('backup.a3_required', true),
        ]);
    }

    public function generateDownloadLink(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tier' => 'required|in:active,archive',
            'file' => 'required|string|max:255',
        ]);

        $fileName = basename((string) $data['file']);
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $fileName)) {
            return back()->withErrors(['file' => AuthMessages::BACKUP_INVALID_FILE_NAME]);
        }

        $path = $this->backupPath((string) $data['tier'], $fileName);
        if (!File::exists($path)) {
            return back()->withErrors(['file' => AuthMessages::BACKUP_FILE_NOT_FOUND]);
        }

        $url = URL::temporarySignedRoute(
            'backup.download',
            now()->addMinutes((int) config('backup.download_link_ttl_minutes', 5)),
            [
                'tier' => $data['tier'],
                'file' => $fileName,
            ]
        );

        $cert = $request->attributes->get('a3_certificate', []);
        Log::info('Backup download link generated', [
            'tier' => $data['tier'],
            'file' => $fileName,
            'user_id' => optional(auth()->user())->getAuthIdentifier(),
            'a3_mode' => $cert['mode'] ?? 'unknown',
            'a3_subject' => $cert['subject'] ?? null,
        ]);
        $user = auth()->user();
        if ($user) {
            app(AuthEventService::class)->registerCustomEvent($request, $user, AuthEventTypes::BACKUP_LINK_GENERATED, [
                'tier' => $data['tier'],
                'file' => $fileName,
            ]);
        }

        return back()->with('download_url', $url);
    }

    public function download(Request $request, string $tier, string $file): BinaryFileResponse
    {
        if (!in_array($tier, ['active', 'archive'], true)) {
            abort(404);
        }

        $fileName = basename($file);
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $fileName)) {
            abort(404);
        }

        $path = $this->backupPath($tier, $fileName);
        if (!File::exists($path)) {
            abort(404);
        }

        $cert = $request->attributes->get('a3_certificate', []);
        Log::info('Backup download executed', [
            'tier' => $tier,
            'file' => $fileName,
            'user_id' => optional(auth()->user())->getAuthIdentifier(),
            'a3_mode' => $cert['mode'] ?? 'unknown',
            'a3_subject' => $cert['subject'] ?? null,
            'ip' => $request->ip(),
        ]);
        $user = auth()->user();
        if ($user) {
            app(AuthEventService::class)->registerCustomEvent($request, $user, AuthEventTypes::BACKUP_DOWNLOAD_EXECUTED, [
                'tier' => $tier,
                'file' => $fileName,
                'a3_mode' => $cert['mode'] ?? 'unknown',
            ]);
        }

        return response()->download($path, $fileName);
    }

    private function listTier(string $tier): array
    {
        $path = $this->backupDir() . DIRECTORY_SEPARATOR . $tier;
        if (!File::isDirectory($path)) {
            return [];
        }

        $entries = collect(File::files($path))
            ->filter(function (\SplFileInfo $file) {
                return in_array(strtolower($file->getExtension()), ['dump', 'sql'], true);
            })
            ->map(function (\SplFileInfo $file) {
                return [
                    'name' => $file->getFilename(),
                    'size_bytes' => $file->getSize(),
                    'modified_at' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            })
            ->sortByDesc('modified_at')
            ->values()
            ->all();

        return $entries;
    }

    private function backupDir(): string
    {
        return (string) config('backup.directory', '/var/backups/ecidade');
    }

    private function backupPath(string $tier, string $fileName): string
    {
        return $this->backupDir() . DIRECTORY_SEPARATOR . $tier . DIRECTORY_SEPARATOR . $fileName;
    }
}
