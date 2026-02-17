<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords {
        reset as protected traitReset;
    }

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/web/welcome';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function reset(Request $request)
    {
        if (!$request->filled('password') && $request->filled('senha')) {
            $request->merge([
                'password' => $request->input('senha'),
                'password_confirmation' => $request->input('senha_confirmation'),
            ]);
        }

        if (!$request->filled('email') && $request->filled('login')) {
            $request->merge([
                'email' => $this->resolveEmailFromLogin((string) $request->input('login')),
            ]);
        }

        return $this->traitReset($request);
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/[A-Za-z]/',
                'regex:/\d/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ];
    }

    protected function resetPassword(CanResetPasswordContract $user, $password)
    {
        $user->senha = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));
        $this->guard()->login($user);
    }

    protected function credentials(Request $request)
    {
        return [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation'),
            'token' => $request->input('token'),
        ];
    }

    private function resolveEmailFromLogin(string $rawLogin): string
    {
        $normalizedCpf = preg_replace('/\D+/', '', trim($rawLogin));

        $user = User::query()
            ->with('cgm')
            ->where('login', trim($rawLogin))
            ->when(!empty($normalizedCpf), function ($query) use ($normalizedCpf) {
                $query->orWhereHas('cgm', function ($subQuery) use ($normalizedCpf) {
                    $subQuery->whereRaw("regexp_replace(z01_cgccpf, '[^0-9]', '', 'g') = ?", [$normalizedCpf]);
                });
            })
            ->first();

        return (string) ($user?->email ?? $user?->cgm?->z01_email ?? 'invalid-reset-target@example.invalid');
    }
}
