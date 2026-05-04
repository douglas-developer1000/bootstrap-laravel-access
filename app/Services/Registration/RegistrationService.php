<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Libraries\Registration\Contracts\HandlerInterface;
use App\Libraries\Utils\PhoneFormatter;
use App\Services\Contracts\RegistrationInterface;
use App\Models\{
    RegisterOrder,
    RegisterApproval,
    User
};
use Carbon\Carbon;

final class RegistrationService implements RegistrationInterface
{
    /** @var array<int, HandlerInterface> */
    protected $handlers;

    public function existsUserByEmail(string $email): bool
    {
        return User::where(['email' => $email])->exists();
    }

    public function findRegisterOrderByEmail(string $email): ?RegisterOrder
    {
        return RegisterOrder::firstWhere('email', $email);
    }

    public function findRegisterApprovalByEmail(string $email): ?RegisterApproval
    {
        return RegisterApproval::firstWhere('email', $email);
    }

    public function createRegisterOrder(string $email, ?string $phone): void
    {
        RegisterOrder::create([
            'email' => $email,
            'phone' => $phone
        ]);
    }

    public function updateModelPhone(RegisterOrder|RegisterApproval $model, ?string $phone): void
    {
        if ($model->phone === PhoneFormatter::clear($phone)) {
            return;
        }
        if ($model instanceof RegisterOrder) {
            RegisterOrder::where(['id' => $model->id])->update([
                'phone' => $phone
            ]);
        } else {
            RegisterApproval::where(['id' => $model->id])->update([
                'phone' => $phone
            ]);
        }
    }

    public function updateRegisterApproval(int $id, string $token, Carbon $expirationData): void
    {
        RegisterApproval::where(['id' => $id])->update([
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
