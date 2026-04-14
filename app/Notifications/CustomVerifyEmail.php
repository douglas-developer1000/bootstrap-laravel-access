<?php

namespace App\Notifications;

// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail;

class CustomVerifyEmail extends VerifyEmail
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        return (new MailMessage)
            ->subject('Verifique seu endereço de e-mail')
            ->view('emails.default-email', [
                'url' => $verificationUrl,
                'logo' => config('mail.logo'),
                'title' => 'Verificação de e-mail',
                'heading' => 'Olá! Bem-vindo!',
                'paragraphs' => [
                    'Por favor, clique no botão abaixo para verificar seu endereço de e-mail.'
                ],
                'btnText' => 'Verifique o E-mail',
                'remain' => [
                    'Se você não criou nenhuma conta, nenhuma ação é necessária.'
                ],
                'regards' => 'Atenciosamente,'
            ]);
    }
}
