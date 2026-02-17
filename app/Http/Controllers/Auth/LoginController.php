<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        Log::warning('Authentication failed', [
            'login' => $request->input('login'),
            'ip' => $request->ip(),
        ]);

        return parent::sendFailedLoginResponse($request);
    }

    protected function authenticated(Request $request, $user)
    {
        Log::info('Authentication succeeded', [
            'user_id' => $user->getAuthIdentifier(),
            'login' => $user->login,
            'ip' => $request->ip(),
        ]);
    }
}
