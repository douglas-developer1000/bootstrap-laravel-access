<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class LicenseActiveMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly License $newLicense,
    ) {
        //
    }

    public function build(): Mailable
    {
        $fromEmail = config('mail.from.address');

        return $this
            ->subject('Licença aprovada')
            ->from($fromEmail, config('app.superadmin.name'))
            ->view('emails.default-email', [
                'url' => route(name: 'login', absolute: true),
                'logo' => config('mail.logo'),
                'title' => 'Aviso de acesso liberado',
                'heading' => 'Parabéns!',
                'paragraphs' => [
                    "Seu acesso ao plano \"{$this->newLicense->plan->name}\" foi liberado.",
                    'A partir de agora, será permitido o uso de todas as funcionalidades desse plano, mais os opcionais escolhidos anteriormente.',
                    ['raw' => true, 'content' => "Qualquer dúvida, contate-nos pelo email <a href='mailto:{$fromEmail}' target='_blank'>{$fromEmail}</a>."],
                ],
                'btnText' => 'Acesse agora',
                'remain' => [
                    'Se você não solicitou nenhum acesso, nenhuma ação é necessária.',
                ],
                'regards' => 'Atenciosamente,',
            ]);
    }
}
