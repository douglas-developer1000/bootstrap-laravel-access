<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Libraries\Registration\Contracts\HandlerInterface;
use App\Libraries\Utils\PhoneFormatter;
use App\Repositories\RegisterOrderRepository;
use App\Repositories\RegisterApprovalRepository;
use App\Services\Contracts\RegistrationInterface;
use App\Models\{
    RegisterOrder,
    RegisterApproval
};
use App\Repositories\UserRepository;
use Carbon\Carbon;

final class RegistrationService implements RegistrationInterface
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

    public function setHandlers(HandlerInterface ...$handlers): RegistrationInterface
    {
        $this->handlers = $handlers;
        return $this;
    }
}
