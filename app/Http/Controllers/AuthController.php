<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Libraries\Utils\DatetimeFormatter;
use App\Services\AuthService;

final class AuthController extends Controller
{
    public function __construct(protected AuthService $svc)
    {
        // ...
    }
    /**
     * Summary of login
     * 
     * @param Request $request
     */
    public function login(AuthRequest $request): RedirectResponse
    {
        ['status' => $status, 'credentials' => $credentials] = $this->svc->login($request);
        if (!$status) {
            return back()->withErrors([
                'generic' => 'Email ou senha inválidos'
            ])->onlyInput('email');
        }
        if (config('app.env') === 'production') {
            $this->logSpecialGuest($credentials['email']);
        }
        return redirect()->route('dashboard');
    }

    /**
     * Summary of logout
     * 
     * @param Request $request
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->svc->logout($request, true);

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
        Log::channel('slack')->info("Convidado autenticado em {$dt}!");
    }
}
