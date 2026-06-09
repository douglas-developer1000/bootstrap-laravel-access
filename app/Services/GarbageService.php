<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Enums\PermissionNameEnum;
use App\Libraries\Enums\StockExitTypeEnum;
use App\Models\StockExit;
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

final class GarbageService
{
    protected User $user;

    /**
     * @var Collection<StockExitTypeEnum> $stockExitTypes;
     */
    protected Collection $stockExitTypes;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->stockExitTypes = $this->defineStockExitTypes();
    }
    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class($this->user, $this->stockExitTypes) extends AbstractPaginatorIndex
        {
            public function __construct(
                protected User $user,
                protected Collection $stockExitTypes
            ) {
                parent::__construct();
            }

            #[Override]
            public function query(Request $request): Builder
            {
                return StockExit::whereBelongsTo($this->user)
                    ->join(
                        'stock_entries',
                        'stock_entries.id',
                        '=',
                        'stock_exits.stock_entry_id'
                    )
                    ->join(
                        'products',
                        'products.id',
                        '=',
                        'stock_entries.product_id'
                    )
                    ->select([
                        'products.name as product',
                        DB::raw('(stock_exits.qty * stock_entries.cost) as cost'),

                        'stock_exits.id',
                        'stock_exits.type',
                        'stock_exits.created_at',
                        'stock_exits.user_id',
                    ])
                    ->getQuery();
            }

            protected function pickLossFilters(Request $request): Collection
            {
                $qs = collect($request->query());
                return $this->stockExitTypes->map(
                    fn(StockExitTypeEnum $exitType) => $exitType->value
                )->mapWithKeys(fn(string $enumKey) => [
                    $enumKey => !$qs->has($enumKey) || $request->boolean($enumKey)
                ]);
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                $lossTypes = $this->pickLossFilters($request)->filter(
                    fn(bool $presence) => $presence === true
                )->keys();

                return parent::attachQuery(
                    $request,
                    $query->whereIn(
                        'type',
                        $lossTypes
                    )
                );
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'type', 'cost', 'product'];
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    protected function defineStockExitTypes(): Collection
    {
        $stockExitTypes = collect();
        if ($this->user->can(PermissionNameEnum::DEMONSTRATION_SHOW)) {
            $stockExitTypes->push(StockExitTypeEnum::DEMONSTRATION);
        }
        if ($this->user->can(PermissionNameEnum::PERSONAL_USE_SHOW)) {
            $stockExitTypes->push(StockExitTypeEnum::PERSONAL_USE);
        }
        if ($this->user->can(PermissionNameEnum::LOSS_SHOW)) {
            $stockExitTypes->push(StockExitTypeEnum::LOSS);
        }
        return $stockExitTypes;
    }

    /**
     * @return array<string, string>
     */
    public function defineGarbageFilter(): array
    {
        return $this->stockExitTypes->mapWithKeys(fn(StockExitTypeEnum $exitType) => [
            $exitType->value => $exitType->toString()
        ])->all();
    }

    public function hydrateStockExit(array $exits): Collection
    {
        return StockExit::hydrate($exits)->map(
            function (StockExit $exit, int $i) use ($exits) {
                $exit->cost = Number::currency(
                    number: (float)$exits[$i]->cost,
                    in: 'BRL',
                    locale: 'pt_BR',
                    precision: 2
                );
                $exit->product = $exits[$i]->product;
                return $exit;
            }
        );
    }
}
