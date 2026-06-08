<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Models\RegisterApproval;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Override;

final class RegisterApprovalService
{
    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class extends AbstractPaginatorIndex
        {
            #[Override]
            public function getSortColumns(): array
            {
                return collect(parent::getSortColumns())->merge([
                    'created_at',
                    'id',
                    'email'
                ])->all();
            }

            #[Override]
            public function query(Request $request): Builder
            {
                return DB::table('register_approvals');
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                $search = $this->paginator->buildSearch($request->only('q'));
                if ($search) {
                    $search = addcslashes($search, '%_');
                    return parent::attachQuery($request, $query)->whereLike(
                        'email',
                        "%{$search}%"
                    );
                }
                return parent::attachQuery($request, $query);
            }
        })->prepareIndex(
            $request,
            'id',
            'email',
            'phone',
            'created_at'
        );
    }

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
