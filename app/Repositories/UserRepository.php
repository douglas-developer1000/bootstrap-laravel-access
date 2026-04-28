<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\AbstractRepository;

class UserRepository extends AbstractRepository
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
}
