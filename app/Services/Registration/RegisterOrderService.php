<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Repositories\RegisterOrderRepository;

final class RegisterOrderService
{
    public function __construct(
        protected readonly RegisterOrderRepository $repository
    ) {
        // ...
    }

    public function delete($id): int
    {
        return $this->repository->delete($id);
    }

    public function removeList(array $ids)
    {
        return $this->repository->destroy($ids);
    }
}
