<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Exchange;
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

final class ExchangeService
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
                return Exchange::whereBelongsTo($this->user)
                    ->join(
                        'stock_exits',
                        'stock_exits.id',
                        '=',
                        'exchanges.stock_exit_id'
                    )
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
                        'exchanges.person',
                        'products.name as product',
                        'exchanges.created_at',
                        DB::raw('(stock_exits.qty * stock_entries.cost) as cost')
                    ])
                    ->getQuery();
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'person', 'cost', 'product'];
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    public function hydrateExchange(array $exchanges): Collection
    {
        return Exchange::hydrate($exchanges)->map(
            function (Exchange $exchange, int $i) use ($exchanges) {
                $exchange->cost = Number::currency(
                    number: (float)$exchanges[$i]->cost,
                    in: 'BRL',
                    locale: 'pt_BR',
                    precision: 2
                );
                return $exchange;
            }
        );
    }

    public function removeExchange(StockExit $exit): void
    {
        $exchange = Exchange::firstWhere(['stock_exit_id' => $exit->id]);
        $exchange->delete();
    }

    public function removeExchangeGroup(array $exits): void
    {
        collect($exits)->each($this->removeExchange(...));
    }
}
