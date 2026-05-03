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
}
