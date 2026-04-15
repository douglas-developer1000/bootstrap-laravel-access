<?php

declare(strict_types=1);

namespace App\Notifications;

// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Libraries\Utils\DatetimeFormatter;

class PosResetPasswordNotification extends Notification
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
        /** @var string $now */
        $now = DatetimeFormatter::formatToDate(
            datetime: now(),
            timeZone: 'America/Sao_Paulo',
            timed: true
        );
        return (new MailMessage)
            ->subject('Alteração de senha')
            ->from(config('mail.from.address'), config('app.superadmin.name'))
            ->view('emails.default-email', [
                'url' => route('password.request'),
                'logo' => config('mail.logo'),
                'title' => 'Alteração de senha',
                'heading' => 'Sua senha foi alterada.',
                'paragraphs' => [
                    "A alteração ocorreu em nossa aplicação em {$now}.",
                    'Se não foi você, sua conta foi comprometida. Clique no botão abaixo para solicitar uma nova senha.'
                ],
                'btnText' => 'Solicitar nova senha',
                'remain' => [
                    'Se foi você, nenhuma ação é necessária.'
                ],
                'regards' => 'Atenciosamente,'
            ]);
    }
}
