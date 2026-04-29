<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\AbstractRepository;

final class CustomerRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(Customer::class);
    }

    public function destroy(array $ids): void
    {
        $this->loadModel()::whereIn('id', $ids)->delete();
    }
}
