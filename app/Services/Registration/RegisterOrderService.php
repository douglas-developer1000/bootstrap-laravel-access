<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Models\RegisterOrder;

final class RegisterOrderService
{
    public function delete(int $id): int
    {
        return RegisterOrder::where(['id' => $id])->delete();
    }

    public function removeList(array $ids)
    {
        return RegisterOrder::whereIn('id', $ids)->delete();
    }
}
