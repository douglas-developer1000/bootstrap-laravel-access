<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class PlanSwitchMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly License $newLicense,
    ) {
        //
    }

    /**
     * @return array<int, string>
     */
    protected function pullParagraphs(string $fromEmail): array
    {
        if ($this->newLicense->price_paid->isZero()) {
            return [
                'O plano de sua licença atual foi modificado aproveitando seus créditos disponíveis.',
                "A partir de agora e sem nenhum custo, sua licença passará a contemplar um novo plano de acesso chamado \"{$this->newLicense->plan->name}\", com funcionalidades diferentes das anteriores.",
                ['raw' => true, 'content' => "Qualquer dúvida, contate-nos pelo email <a href='mailto:{$fromEmail}' target='_blank'>{$fromEmail}</a>."],
            ];
        }

        return [
            "A troca para um plano de acesso novo e diferente, chamado \"{$this->newLicense->plan->name}\", foi solicitado e está aguardando liberação.",
            'Até que possamos confirmar seu pagamento, você pode continar utilizando normalmente seu plano de acesso atual.',
            'Nossa equipe de suporte entrará em contato com você por email, telefone ou WhatsApp para acertar alguns detalhes quanto a essa liberação.',
            ['raw' => true, 'content' => "Qualquer dúvida, contate-nos pelo email <a href='mailto:{$fromEmail}' target='_blank'>{$fromEmail}</a>."],
        ];
    }

    public function build()
    {
        $fromEmail = config('mail.from.address');

        return $this
            ->subject('Mudança de plano de acesso')
            ->from($fromEmail, config('app.superadmin.name'))
            ->view('emails.default-email', [
                'url' => route(name: 'login', absolute: true),
                'logo' => config('mail.logo'),
                'title' => 'Aviso de troca de plano',
                'heading' => 'Renovando o calhambeque...',
                'paragraphs' => $this->pullParagraphs($fromEmail),
                'btnText' => 'Veja se seu novo acesso foi liberado',
                'remain' => [
                    'Se você não solicitou nenhum novo acesso, nenhuma ação é necessária.',
                ],
                'regards' => 'Atenciosamente,',
            ]);
    }
}
