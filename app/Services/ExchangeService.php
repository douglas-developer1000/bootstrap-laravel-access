<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Enums\StockExitTypeEnum;
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
    protected User $user;
    public function __construct()
    {
        $this->user = Auth::user();
    }
    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class($this->user) extends AbstractPaginatorIndex
        {
            public function __construct(protected User $user)
            {
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
                        'stock_exits.id as exitId',
                        'stock_exits.type as exitType',
                        'products.name as product',
                        DB::raw('(stock_exits.qty * stock_entries.cost) as cost'),

                        'exchanges.id',
                        'exchanges.person',
                        'exchanges.created_at',
                        'exchanges.user_id',
                        'exchanges.stock_exit_id',
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
            function (Exchange $exchange, int $i) use (&$exchanges) {
                $exchange->exit = tap(new StockExit(), function (StockExit $exit) use (&$exchanges, &$i) {
                    $exit->id = $exchanges[$i]->exitId;
                    $exit->user_id = $this->user->id;
                    $exit->type = StockExitTypeEnum::from($exchanges[$i]->exitType);
                });
                $exchange->product = $exchanges[$i]->product;
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

    public function removeExchange(Exchange $exchange): void
    {
        $exchange->delete();
    }

    /**
     * @param Exchange[] $exchanges
     * @return StockExit[]
     */
    public function removeExchangeGroup(array $exchanges): array
    {
        return collect($exchanges)->map(function (Exchange $exchange) {
            /**
             * @var StockExit $exit
             */
            $exit = $exchange->stockExit;

            $this->removeExchange($exchange);

            return $exit;
        })->all();
    }
}
