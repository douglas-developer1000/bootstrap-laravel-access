<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Models\RegisterApproval;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
                return RegisterApproval::getQuery();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                return parent::attachQuery($request, $query)->when(
                    $this->paginator->buildSearch($request->only('q')),
                    function (Builder $query, string $search) {
                        $search = addcslashes($search, '%_');
                        return $query->whereLike(
                            'email',
                            "%{$search}%"
                        );
                    }
                );
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
