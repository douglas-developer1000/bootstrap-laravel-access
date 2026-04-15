<?php

namespace App\Notifications;

// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;

final class CustomResetPasswordNotification extends ResetPassword
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        parent::__construct($token);
    }

    protected function resetUrl($notifiable)
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);
        return (new MailMessage)
            ->subject('Redefina sua senha')
            ->from(config('mail.from.address'), config('app.superadmin.name'))
            ->view('emails.default-email', [
                'url' => $resetUrl,
                'logo' => config('mail.logo'),
                'title' => 'Redefina sua senha',
                'heading' => 'Olá!',
                'paragraphs' => [
                    'Você está recebendo este e-mail pois nós recebemos uma requisição de redefinição de senha para sua conta.'
                ],
                'btnText' => 'Redefina a senha',
                'remain' => [
                    'Este link de redefinição de senha expirará em 60 minutos.',
                    'Se você não requisitou uma redefinição de senha, nenhuma ação é necessária.'
                ],
                'regards' => 'Atenciosamente,'
            ]);
    }
}
