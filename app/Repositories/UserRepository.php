<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\AbstractRepository;

final class UserRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function destroy(array $ids, bool $forceDelete): void
    {
        $query = $this->loadModel()::whereIn('id', $ids);
        if ($forceDelete) {
            $query->forceDelete();
        } else {
            $query->delete();
        }
    }

    public function restore(array $ids): void
    {
        User::withTrashed()->whereIn('id', $ids)->restore();
    }
}
