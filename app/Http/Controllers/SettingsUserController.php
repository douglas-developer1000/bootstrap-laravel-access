<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SettingsUser\SettingsUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

final class SettingsUserController extends Controller
{
    use AuthorizesRequests;

    public function show(Request $request)
    {
        /** @var Authenticatable $user */
        $user = $request->user();
        return view('pages.settings.user.show', ['user' => $user]);
    }

    public function edit(User $user)
    {
        return view('pages.settings.user.edit', ['user' => $user]);
    }

    /**
     * Update the user's data by the authenticated user themselves
     */
    public function update(SettingsUserRequest $request, UserService $userSvc, User $user)
    {
        $this->authorize('update', $user);
        $userSvc->updateUserByOwner($request, $user);

        return redirect()->route('settings.user.show')->with([
            'toastShow' => true,
            'toastMsg' => 'Dados da conta editados com sucesso!'
        ]);
    }
}
