<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SettingsUser\SettingsUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class SettingsUserController extends Controller
{
    protected User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function show(): View
    {
        return view('pages.settings.user.show', [
            'user' => $this->user,
            'activeLicense' => $this->user->activeLicense,

            'creditValue' => $this->user->credits()->sum('amount'),
        ]);
    }

    public function edit(User $user): View
    {
        return view('pages.settings.user.edit', ['user' => $user]);
    }

    /**
     * Update the user's data by the authenticated user themselves
     */
    public function update(SettingsUserRequest $request, UserService $userSvc, User $user): RedirectResponse
    {
        $userSvc->updateUserByOwner($request, $user);

        return redirect()->route('settings.user.show')->with([
            'toastShow' => true,
            'toastMsg' => 'Dados da conta editados com sucesso!',
        ]);
    }
}
