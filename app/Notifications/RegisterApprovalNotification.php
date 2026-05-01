<?php

declare(strict_types=1);

namespace App\Notifications;

// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use \Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

final class RegisterApprovalNotification extends Notification
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Aprovação de registro')
            ->from(config('mail.from.address'), config('app.superadmin.name'))
            ->view('emails.default-email', [
                'url' => URL::temporarySignedRoute(
                    name: 'guest.users.create',
                    expiration: Carbon::now()->addMinutes(
                        \intval(config('registration.timeout.email'))
                    ),
                    parameters: ['token' => $notifiable->token]
                ),
                'logo' => config('mail.logo'),
                'title' => 'Aprovação de registro',
                'heading' => 'Parabéns!',
                'paragraphs' => [
                    'A partir de agora, você poderá registrar sua nova conta.',
                    'Para começar a usar, primeiro cadastre seus dados clicando no botão abaixo:'
                ],
                'btnText' => 'Clique aqui',
                'remain' => [
                    'Se você não solicitou uma conta, nenhuma ação é necessária.'
                ],
                'regards' => 'Atenciosamente,'
            ]);
    }
}
