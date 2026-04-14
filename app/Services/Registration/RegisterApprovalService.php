<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Models\RegisterApproval;
use App\Repositories\RegisterApprovalRepository;

final class RegisterApprovalService
{
    public function __construct(
        protected readonly RegisterApprovalRepository $approvalRepository
    ) {
        // ...
    }

    public function findByEmail(string $email)
    {
        return $this->approvalRepository->findByEmail($email);
    }

    public function delete($id): int
    {
        return $this->approvalRepository->delete($id);
    }

    public function create(array $attributes = []): ?RegisterApproval
    {
        return $this->approvalRepository->create($attributes);
    }
}
