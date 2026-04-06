<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AuthController extends Controller
{
    /**
     * Summary of login
     * 
     * @param Request $request
     */
    public function login(AuthRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'generic' => 'Email ou senha inválidos'
            ])->onlyInput('email');
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
}
