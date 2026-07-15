<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Enums\PaymentTypeEnum;
use App\Models\Customer;
use App\Models\PaymentCard;
use App\Models\Sale;
use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Override;

final class SaleService
{
    protected User $user;

    public function __construct(protected StockExitService $exitSvc)
    {
        $this->user = Auth::user();
    }

    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class($this->user, $this) extends AbstractPaginatorIndex
        {
            public function __construct(
                protected User $user,
                protected SaleService $svc,
            ) {
                return parent::__construct();
            }

            #[Override]
            public function query(Request $request): Builder
            {
                return Sale::whereBelongsTo($this->user)
                    ->join(
                        'payments',
                        'sales.id',
                        '=',
                        'payments.sale_id'
                    )
                    ->join(
                        'customers',
                        'customers.id',
                        '=',
                        'payments.customer_id'
                    )
                    ->select([
                        'sales.id',
                        'sales.created_at',
                        'sales.user_id',
                        'customers.name as customer',
                        DB::raw('SUM(payments.value) as value'),
                    ])
                    ->groupBy([
                        'sales.id',
                        'sales.created_at',
                        'sales.user_id',
                        'sales.discount_id',
                        'customer',
                    ])->getQuery();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                $payTypes = $this->pickPayTypeFilters($request)->filter(
                    fn (bool $presence) => $presence === true
                )->keys();
                $query = parent::attachQuery(
                    $request,
                    $query->whereIn(
                        'payments.type',
                        $payTypes
                    )
                );
                if ($this->user->cannot('viewAny', Customer::class)) {
                    return $query;
                }

                return $this->filterSearch(
                    $query,
                    $this->paginator->buildSearch($request->only('q')),
                    'name'
                );
            }

            protected function pickPayTypeFilters(Request $request): Collection
            {
                $qs = collect($request->query());

                return collect($this->svc->definePayTypes())
                    ->map(fn (PaymentTypeEnum $payType) => $payType->value)
                    ->mapWithKeys(fn (string $enumKey) => [
                        $enumKey => ! $qs->has($enumKey) || $request->boolean($enumKey),
                    ]);
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'value', 'customer'];
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    public function definePayTypes(): array
    {
        $payTypes = [
            PaymentTypeEnum::PIX,
            PaymentTypeEnum::MONEY,
        ];
        if ($this->user->can('viewAny', PaymentCard::class)) {
            return [
                ...$payTypes,
                PaymentTypeEnum::CARD,
            ];
        }

        return $payTypes;
    }

    public function hydrateSales(array $sales): Collection
    {
        return Sale::hydrate($sales)->map(function (Sale $sale, int $i) use (&$sales) {
            $sale->customer = $sales[$i]->customer;
            $sale->value = Number::currency(
                number: (float) $sales[$i]->value,
                in: 'BRL',
                locale: 'pt_BR',
                precision: 2
            );

            return $sale;
        });
    }

    public function removeSale(Sale $sale): void
    {
        $this->exitSvc->removeStockExitGroup($sale->stockExits->all());
        $sale->delete();
    }

    /**
     * @param  Sale[]  $sales
     */
    public function removeSaleGroup(array $sales): void
    {
        collect($sales)->each($this->removeSale(...));
    }
}
