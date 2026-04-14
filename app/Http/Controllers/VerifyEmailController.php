<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function verify()
    {
        return view('pages.verify-email');
    }

    public function handle(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('dashboard');
    }

    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with([
            'toastShow' => true,
            'toastMsg' => 'Um novo link de verificação foi enviado para seu email.'
        ]);
    }
}
