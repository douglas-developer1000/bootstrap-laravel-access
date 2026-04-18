<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libraries\Utils\DatetimeFormatter;

final class AuthController extends Controller
{
    /**
     * Summary of login
     * 
     * @param Request $request
     */
    public function login(AuthRequest $request): RedirectResponse
    {
        $remember = $request->filled('remember');
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'generic' => 'Email ou senha inválidos'
            ])->onlyInput('email');
        }
        if (config('app.env') === 'production') {
            $this->logSpecialGuest($credentials['email']);
        }
        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }

    /**
     * Summary of logout
     * 
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function logSpecialGuest(string $email): void
    {
        if ($email !== config('auth.special-guest-email')) {
            return;
        }
        $dt = DatetimeFormatter::formatToDate(
            datetime: now(),
            timed: true
        );
        Log::channel('slack')->warning("Convidado autenticado em {$dt}!");
    }
}
