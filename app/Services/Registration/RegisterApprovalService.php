<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Models\RegisterApproval;

final class RegisterApprovalService
{
    public function findByEmail(?string $email): ?RegisterApproval
    {
        if ($email === NULL) {
            return NULL;
        }
        return RegisterApproval::firstWhere('email', $email);
    }

    public function create(array $attributes = []): ?RegisterApproval
    {
        return RegisterApproval::create($attributes);
    }

    public function removeRegisterApproval(int $id)
    {
        return RegisterApproval::where(['id' => $id])->delete();
    }

    public function removeRegisterApprovalGroup(array $ids)
    {
        return RegisterApproval::whereIn('id', $ids)->delete();
    }
}
