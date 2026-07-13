<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class LicensePendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Plan $newPlan,
    ) {
        //
    }

    public function build(): Mailable
    {
        $fromEmail = config('mail.from.address');

        return $this
            ->subject('Licença aguardando aprovação')
            ->from($fromEmail, config('app.superadmin.name'))
            ->view('emails.default-email', [
                'url' => route(name: 'login', absolute: true),
                'logo' => config('mail.logo'),
                'title' => 'Aviso de licença criada',
                'heading' => 'Aquecendo os motores...',
                'paragraphs' => [
                    "Sua licença referente ao plano \"{$this->newPlan->name}\" foi criada e está aguardando liberação.",
                    'Nossa equipe de suporte entrará em contato com você por email, telefone ou WhatsApp para acertar alguns detalhes quanto ao seu acesso.',
                    ['raw' => true, 'content' => "Qualquer dúvida, contate-nos pelo email <a href='mailto:{$fromEmail}' target='_blank'>{$fromEmail}</a>."],
                ],
                'btnText' => 'Veja se seu acesso foi liberado',
                'remain' => [
                    'Se você não solicitou nenhum acesso, nenhuma ação é necessária.',
                ],
                'regards' => 'Atenciosamente,',
            ]);
    }
}
