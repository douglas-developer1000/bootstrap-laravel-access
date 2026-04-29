<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;

final class UserService
{
    public function __construct(protected UserRepository $repository)
    {
        // ...
    }

    public function create(array $attributes)
    {
        return $this->repository->create($attributes);
    }

    public function update(int $id, array $attributes = []): int
    {
        return $this->repository->update($id, $attributes);
    }

    public function removeList(array $ids, bool $forceDelete = false)
    {
        return $this->repository->destroy($ids, $forceDelete);
    }

    public function restoreList(array $ids)
    {
        $this->repository->restore($ids);
    }
}
