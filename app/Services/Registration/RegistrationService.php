<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Libraries\Registration\Contracts\HandlerInterface;
use App\Libraries\Utils\PhoneFormatter;
use App\Mail\DefaultEmail;
use App\Repositories\RegisterOrderRepository;
use App\Repositories\RegisterApprovalRepository;
use App\Services\Contracts\RegistrationServiceInterface;
use App\Models\{
    RegisterOrder,
    RegisterApproval
};
use \Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\URL;

final class RegistrationService implements RegistrationServiceInterface
{
    /** @var array<int, HandlerInterface> */
    protected $handlers;

    public function __construct(
        protected readonly RegisterOrderRepository $registerOrderRepository,
        protected readonly RegisterApprovalRepository $approvalRepository,
        protected readonly UserRepository $userRepository,
    ) {
        // ...
    }

    public function existsUserByEmail(string $email): bool
    {
        return $this->userRepository->exists(['email' => $email]);
    }

    public function findRegisterOrderByEmail(string $email): ?RegisterOrder
    {
        return $this->registerOrderRepository->findByEmail($email);
    }

    public function findRegisterApprovalByEmail(string $email): ?RegisterApproval
    {
        return $this->approvalRepository->findByEmail($email);
    }

    public function createRegisterOrder(string $email, ?string $phone): void
    {
        $this->registerOrderRepository->create(
            attributes: [
                'email' => $email,
                'phone' => $phone
            ]
        );
    }

    public function updateModelPhone(RegisterOrder|RegisterApproval $model, ?string $phone): void
    {
        if ($model->phone === PhoneFormatter::clear($phone)) {
            return;
        }
        $repository = $model instanceof RegisterOrder ? $this->registerOrderRepository : $this->approvalRepository;
        $repository->update(
            id: $model->id,
            attributes: [
                'phone' => $phone
            ]
        );
    }

    public function updateRegisterApproval(int $id, string $token, Carbon $expirationData): void
    {
        $this->approvalRepository->update(id: $id, attributes: [
            'token' => $token,
            'expiration_data' => $expirationData
        ]);
    }

    public function sendApprovalMail(string $email, string $token): void
    {
        Mail::to($email)->send(new DefaultEmail([
            'fromName' => config('app.name'),
            'fromEmail' => config('mail.from.address'),
            'subject' => 'Aprovação de registro',
            'url' => URL::temporarySignedRoute(
                name: 'guest.users.create',
                expiration: Carbon::now()->addMinutes(
                    \intval(config('registration.timeout.email'))
                ),
                parameters: ['token' => $token]
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
        ]));
    }

    public function handleRegister(string $email, ?string $phone): void
    {
        $finalize = collect($this->handlers)->reduce(function ($acc, $next) use ($email, $phone) {
            if ($acc) {
                return $next->handle($email, $phone);
            }
            return false;
        }, true);

        if ($finalize) {
            $this->createRegisterOrder($email, $phone);
        }
    }

    public function setHandlers(HandlerInterface ...$handlers): RegistrationServiceInterface
    {
        $this->handlers = $handlers;
        return $this;
    }
}
