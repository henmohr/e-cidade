<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthEventService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'login';
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'senha' => 'required|string',
        ]);

        $this->ensureNotProgressivelyBlocked($request);
    }

    protected function credentials(Request $request)
    {
        return [
            'login' => $request->input('login'),
            'senha' => $request->input('senha'),
        ];
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $this->registerProgressiveFailure($request);
        app(AuthEventService::class)->registerFailure($request, (string) $request->input('login'));

        Log::warning('Authentication failed', [
            'login' => $request->input('login'),
            'ip' => $request->ip(),
        ]);

        return parent::sendFailedLoginResponse($request);
    }

    protected function authenticated(Request $request, $user)
    {
        $this->clearProgressiveLock($request);
        /** @var AuthEventService $eventService */
        $eventService = app(AuthEventService::class);
        $eventService->registerSuccess($request, $user);
        $failureCount = $eventService->absorbPendingFailuresForUser($user);
        if ($failureCount > 0) {
            session()->flash('auth_warning', "Detectamos {$failureCount} tentativa(s) de acesso mal sucedida(s) antes deste login.");
        }

        Log::info('Authentication succeeded', [
            'user_id' => $user->getAuthIdentifier(),
            'login' => $user->login,
            'ip' => $request->ip(),
        ]);
    }

    public function maxAttempts()
    {
        return (int) config('auth.login_hardening.max_attempts', 5);
    }

    public function decayMinutes()
    {
        return (int) config('auth.login_hardening.decay_minutes', 2);
    }

    private function ensureNotProgressivelyBlocked(Request $request): void
    {
        $blockedUntil = (int) Cache::get($this->progressiveBlockUntilKey($request), 0);
        if ($blockedUntil <= time()) {
            return;
        }

        throw ValidationException::withMessages([
            'login' => [sprintf(
                'Acesso temporariamente bloqueado. Tente novamente em %d segundos.',
                max(1, $blockedUntil - time())
            )],
        ]);
    }

    private function registerProgressiveFailure(Request $request): void
    {
        $windowMinutes = (int) config('auth.login_hardening.progressive_window_minutes', 60);
        $countKey = $this->progressiveFailuresKey($request);

        if (!Cache::has($countKey)) {
            Cache::put($countKey, 0, now()->addMinutes($windowMinutes));
        }

        $failures = (int) Cache::increment($countKey);
        $blockSeconds = $this->computeProgressiveBlockSeconds($failures);

        if ($blockSeconds <= 0) {
            return;
        }

        Cache::put(
            $this->progressiveBlockUntilKey($request),
            Carbon::now()->addSeconds($blockSeconds)->timestamp,
            now()->addSeconds($blockSeconds)
        );
    }

    private function clearProgressiveLock(Request $request): void
    {
        Cache::forget($this->progressiveFailuresKey($request));
        Cache::forget($this->progressiveBlockUntilKey($request));
    }

    private function progressiveFailuresKey(Request $request): string
    {
        return 'auth:login:failures:' . sha1(strtolower(trim((string) $request->input('login'))) . '|' . $request->ip());
    }

    private function progressiveBlockUntilKey(Request $request): string
    {
        return 'auth:login:block_until:' . sha1(strtolower(trim((string) $request->input('login'))) . '|' . $request->ip());
    }

    private function computeProgressiveBlockSeconds(int $failures): int
    {
        if ($failures >= 12) {
            return (int) config('auth.login_hardening.block_tertiary_seconds', 1800);
        }

        if ($failures >= 8) {
            return (int) config('auth.login_hardening.block_secondary_seconds', 600);
        }

        if ($failures >= 5) {
            return (int) config('auth.login_hardening.block_primary_seconds', 120);
        }

        return 0;
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            app(AuthEventService::class)->registerLogout($request, $user);
            Log::info('Authentication logout', [
                'user_id' => $user->getAuthIdentifier(),
                'login' => $user->login ?? null,
                'ip' => $request->ip(),
            ]);
        }

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
