<?php

namespace App\Notifications;

// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail;

class VerifyEmailNotification extends VerifyEmail
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        return (new MailMessage)
            ->subject('Verifique seu endereço de e-mail')
            ->from(config('mail.from.address'), config('app.superadmin.name'))
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
