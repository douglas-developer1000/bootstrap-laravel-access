<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Enums\StockExitTypeEnum;
use App\Models\StockExit;
use App\Services\Contracts\StockExitHandlerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class StockExitService
{
    /**
     * @return array{
     *     type: StockExitTypeEnum,
     *     qty: int,
     *     stock_entry_id: int
     * }[]
     */
    public function extractParams(Request $request, StockExitTypeEnum $exitType): array
    {
        return collect($request->input('entries'))->reject(
            fn(array $qts) => collect($qts)->every(
                fn(string $qty) => $qty === '0'
            )
        )->map(
            fn(array $entries) => collect($entries)->reject(
                fn($qty) => $qty === '0'
            )->map(fn($qty, $id) => [
                'type' => $exitType,
                'qty' => $qty,
                'stock_entry_id' => $id
            ])->values()->all()
        )->all();
    }

    /**
     * @param array<int, array{
     *      type: StockExitTypeEnum,
     *      qty: int,
     *      stock_entry_id: int
     * }[]> $products Each product has its stock entry list
     * 
     * @return Collection<int, Collection<StockExit>>
     */
    public function makeStockExits(array $products): Collection
    {
        return collect($products)->map(
            fn(array $entries) => collect($entries)->map(
                fn(array $entryParams) => StockExit::create([
                    'type' => $entryParams['type'],
                    'qty' => $entryParams['qty'],
                    'user_id' => Auth::id(),
                    'stock_entry_id' => $entryParams['stock_entry_id'],
                ])
            )
        );
    }

    /**
     * Summary of handleStockExits
     * @param StockExitHandlerInterface $handler
     * @param Collection<int, Collection<StockExit>> $exists
     * @return void
     */
    public function handleStockExits(
        StockExitHandlerInterface $handler,
        Request $request,
        Collection $exists,
    ): void {
        $handler->handle($request, $exists);
    }

    public function removeStockExit(StockExit $exit): void
    {
        $exit->delete();
    }

    public function removeStockExitGroup(array $stockExits): void
    {
        collect($stockExits)->each($this->removeStockExit(...));
    }
}
