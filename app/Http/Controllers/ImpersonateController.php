<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;

final class ImpersonateController extends Controller
{
    public function __construct(protected AuthService $svc)
    {
        // ...
    }

    public function login(Request $request, User $user): RedirectResponse
    {
        /** @var User $admin */
        $admin = $request->user();
        $this->svc->logout($request, true);
        $this->svc->impersonateLogin($admin->id, $user->id);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $isOut = $this->svc->logout($request);
        if ($isOut) {
            return redirect()->route('login');
        }
        return redirect()->route('dashboard');
    }
}
