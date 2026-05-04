<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Libraries\Traits\BuildTokenTrait;
use App\Models\RegisterOrder;

final class RegisterOrderService
{
    use BuildTokenTrait;
    public function prepareRegisterApproval(RegisterOrder $order): array
    {
        RegisterOrder::where(['id' => $order->id])->delete();
        return [
            'email' => $order->email,
            'token' => $this->buildToken(),
            'expiration_data' => now()->addHours(
                \intval(
                    config('registration.timeout.token')
                )
            ),
            ...($order->phone ? ['phone' => $order->phone] : [])
        ];
    }

    public function removeRegisterOrder(int $id)
    {
        RegisterOrder::where(['id' => $id])->delete();
    }

    public function removeRegisterOrderGroup(array $ids)
    {
        return RegisterOrder::whereIn('id', $ids)->delete();
    }

    public function findOrdersToApprove(array $ids)
    {
        return RegisterOrder::whereIn('id', $ids)->get(['id', 'email', 'phone']);
    }
}
