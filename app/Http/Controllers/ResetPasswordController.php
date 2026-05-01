<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ResetPassword\ResetPasswordRequest;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;

final class ResetPasswordController extends Controller
{
    public function reset(string $token)
    {
        return view('pages.reset-password', ['token' => $token]);
    }

    public function update(ResetPasswordRequest $request, PasswordService $svc)
    {
        $status = $svc->reset(
            $request->only(['email', 'password', 'password_confirmation', 'token']),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
        if ($status['ok'] === FALSE) {
            return back()->with('error', $status['message']);
        }

        return redirect()->route('login')->with([
            'toastShow' => true,
            'toastMsg' => $status['message']
        ]);
    }
}
