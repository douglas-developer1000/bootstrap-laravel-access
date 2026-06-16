<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Libraries\Traits\BuildTokenTrait;
use App\Models\RegisterOrder;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Override;

final class RegisterOrderService
{
    use BuildTokenTrait;

    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class extends AbstractPaginatorIndex
        {
            #[Override]
            public function getSortColumns(): array
            {
                return collect(parent::getSortColumns())->merge(
                    ['created_at', 'id', 'email']
                )->all();
            }

            #[Override]
            public function query(Request $request): Builder
            {
                return RegisterOrder::getQuery();
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

    public function prepareRegisterApproval(RegisterOrder $order): array
    {
        RegisterOrder::where(['id' => $order->id])->delete();
        return [
            'email' => $order->email,
            'token' => $this->buildToken(),
            'expiration_data' => now()->addHours(
                \intval(
                    config('registration.timeout.token')
                )
            ),
            ...($order->phone ? ['phone' => $order->phone] : [])
        ];
    }

    public function removeRegisterOrder(int $id)
    {
        RegisterOrder::where(['id' => $id])->delete();
    }

    public function removeRegisterOrderGroup(array $ids)
    {
        return RegisterOrder::whereIn('id', $ids)->delete();
    }

    public function findOrdersToApprove(array $ids)
    {
        return RegisterOrder::whereIn('id', $ids)->get(['id', 'email', 'phone']);
    }
}
