<?php

declare(strict_types=1);

namespace App\Services;

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

final class LossService
{
    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class extends AbstractPaginatorIndex
        {
            protected User $user;

            public function __construct()
            {
                $this->user = Auth::user();
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
                        'stock_exits.id',
                        'stock_exits.type',
                        'products.name as product',
                        'stock_exits.created_at',
                        DB::raw('(stock_exits.qty * stock_entries.cost) as cost')
                    ])
                    ->getQuery();
            }

            protected function pickLossFilters(Request $request): Collection
            {
                $qs = collect($request->query());
                return collect([
                    StockExitTypeEnum::DEMONSTRATION->value,
                    StockExitTypeEnum::PERSONAL_USE->value,
                    StockExitTypeEnum::LOSS->value,
                ])->mapWithKeys(fn(string $enumKey) => [
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
