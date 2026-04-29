<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Contracts\AbstractRepository;
use App\Models\RegisterOrder;
use Illuminate\Pagination\LengthAwarePaginator;

final class RegisterOrderRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(RegisterOrder::class);
    }

    /**
     * Query the RegisterRequest instance pagination list
     */
    public function paginate(int $page, int $group, ?string $email = NULL): LengthAwarePaginator
    {
        $query = $this->loadModel()::query()->select('id', 'email');
        if ($email) {
            $query = $query->where([
                ['email', 'like', "%{$email}%"]
            ]);
        }
        return $query->paginate(
            page: $page,
            perPage: $group,
        );
    }

    /**
     * Search an RegisterRequest instance by email
     */
    public function findByEmail(?string $email): ?RegisterOrder
    {
        if ($email === NULL) {
            return NULL;
        }
        return $this->loadModel()::query()->firstWhere('email', $email);
    }

    public function destroy(array $ids)
    {
        $this->loadModel()::whereIn('id', $ids)->delete();
    }
}
