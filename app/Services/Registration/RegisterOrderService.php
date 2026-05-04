<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Libraries\Traits\BuildTokenTrait;
use App\Models\RegisterOrder;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
                return RegisterOrder::query();
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
