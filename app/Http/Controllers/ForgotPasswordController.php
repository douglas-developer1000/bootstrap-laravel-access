<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPassword\ForgotPasswordRequest;
use App\Services\Contracts\RegistrationInterface;
use App\Services\PasswordService;

final class ForgotPasswordController extends Controller
{
    public function screen()
    {
        return view('pages.forgot-password');
    }

    public function ask(
        ForgotPasswordRequest $request,
        RegistrationInterface $registerSvc,
        PasswordService $passSvc
    ) {
        $inputs = $request->only('email');
        $userExists = $registerSvc->existsUserByEmail($inputs['email']);
        if (!$userExists) {
            /** 
             * Dont show the standard message to invalid user (email)
             * because security issue
             **/
            return back()->with([
                'toastShow' => true,
                'toastMsg' => $passSvc->getResetLinkSentMsg()
            ]);
        }

        ['ok' => $ok, 'message' => $message] = $passSvc->sendResetLink($inputs);
        if (!$ok) {
            return back()->with('error', $message);
        }
        return back()->with([
            'toastShow' => true,
            'toastMsg' => $message
        ]);
    }
}
