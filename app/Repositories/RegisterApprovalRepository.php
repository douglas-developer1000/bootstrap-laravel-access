<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Contracts\AbstractRepository;
use App\Models\RegisterApproval;
use Illuminate\Pagination\LengthAwarePaginator;

final class RegisterApprovalRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(RegisterApproval::class);
    }

    /**
     * Query the RegisterApproval instance pagination list
     */
    public function paginate(int $page, int $group, ?string $email = NULL): LengthAwarePaginator
    {
        $query = $this->loadModel()::query()->select('id', 'email', 'phone');
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
     * Search an RegisterPermission instance by email
     */
    public function findByEmail(?string $email): ?RegisterApproval
    {
        if ($email === NULL) {
            return NULL;
        }
        return $this->loadModel()::query()->firstWhere('email', $email);
    }
}
