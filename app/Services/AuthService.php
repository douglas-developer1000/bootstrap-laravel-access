<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AuthService
{
    /**
     * Execute the application's login
     * 
     * @return array{status: bool, credentials: array}
     */
    public function login(Request $request): array
    {
        $remember = $request->filled('remember');
        $credentials = $request->only(['email', 'password']);
        if (!Auth::attempt($credentials, $remember)) {
            return ['status' => false, 'credentials' => $credentials];
        }
        $request->session()->regenerate();
        return ['status' => true, 'credentials' => $credentials];
    }

    /**
     * Execute the application's logout
     */
    public function logout(Request $request, bool $flush = false): bool
    {
        $session = $request->session();
        if (!$flush && $session->has('impersonate-owner')) {
            Auth::loginUsingId($session->pull('impersonate-owner'));
            return false;
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return true;
    }

    public function impersonateLogin(string|int $adminId, string|int $id): Authenticatable|false
    {
        session()->put('impersonate-owner', $adminId);
        return Auth::loginUsingId($id);
    }
}
