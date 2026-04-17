<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\SettingsUserRequest;
use App\Libraries\Utils\PhoneFormatter;
use App\Models\User;
use App\Services\Contracts\ImgStoragerInterface;
use App\Services\ProfileService;
use App\Services\UserService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

final class SettingsUserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected UserService $userSvc,
    ) {
        // ...
    }

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

    public function update(SettingsUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $phone = PhoneFormatter::clear($request->validated('phone'));

        $profileSvc = new ProfileService(
            app(ImgStoragerInterface::class, [
                'model' => $user,
                'key' => 'photo',
                'lastFolderName' => \strval($user->id)
            ])
        );

        $photoPath = $profileSvc->storageProfileImg($request);

        $inputs = collect([
            ...$request->only(['name', 'password']),
            ...($photoPath ? ['photo' => $photoPath] : []),
            'phone' => $phone,
        ])->filter(fn($val, $key) => $user->$key !== $val);

        if ($inputs->isNotEmpty()) {
            $this->userSvc->update($user->id, $inputs->toArray());
        }

        return redirect()->route('settings.user.show')->with([
            'toastShow' => true,
            'toastMsg' => 'Dados da conta editados com sucesso!'
        ]);
    }
}
