<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPassword\ForgotPasswordRequest;
use App\Services\PasswordService;

class ForgotPasswordController extends Controller
{
    public function __construct(protected PasswordService $svc)
    {
        // ...
    }

    public function screen()
    {
        return view('pages.forgot-password');
    }

    public function ask(ForgotPasswordRequest $request)
    {
        ['ok' => $ok, 'message' => $message] = $this->svc->sendResetLink(
            $request->only('email')
        );
        if (!$ok) {
            return back()->with('error', $message);
        }
        return back()->with([
            'toastShow' => true,
            'toastMsg' => $message
        ]);
    }
}
