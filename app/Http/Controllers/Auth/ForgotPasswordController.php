<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\Auth\PasswordResetMessages;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validateEmail(Request $request)
    {
        $request->validate([
            'login' => 'required_without:email|string|max:100',
            'email' => 'required_without:login|email|max:150',
        ]);
    }

    protected function credentials(Request $request): array
    {
        if (!empty($request->input('email'))) {
            return ['email' => (string) $request->input('email')];
        }

        $rawLogin = trim((string) $request->input('login'));
        $normalizedCpf = preg_replace('/\D+/', '', $rawLogin);

        $user = User::query()
            ->with('cgm')
            ->where('login', $rawLogin)
            ->when(!empty($normalizedCpf), function ($query) use ($normalizedCpf) {
                $query->orWhereHas('cgm', function ($subQuery) use ($normalizedCpf) {
                    $subQuery->whereRaw("regexp_replace(z01_cgccpf, '[^0-9]', '', 'g') = ?", [$normalizedCpf]);
                });
            })
            ->first();

        $email = $user?->email ?? $user?->cgm?->z01_email;

        return ['email' => $email ?: PasswordResetMessages::INVALID_TARGET_EMAIL];
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $status = $this->broker()->sendResetLink($this->credentials($request));

        if ($status !== Password::RESET_LINK_SENT) {
            Log::warning('Password reset request did not resolve a valid account', [
                'login' => $request->input('login'),
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);
        }

        return $this->sendResetLinkResponse($request, Password::RESET_LINK_SENT);
    }

    protected function sendResetLinkResponse(Request $request, $response)
    {
        $message = PasswordResetMessages::RESET_LINK_REQUEST_ACCEPTED;

        if ($request->expectsJson()) {
            return new JsonResponse(['message' => $message]);
        }

        return back()->with('status', $message);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return $this->sendResetLinkResponse($request, $response);
    }
}
