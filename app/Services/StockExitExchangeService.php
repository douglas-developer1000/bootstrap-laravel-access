<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Exchange;
use App\Services\Contracts\StockExitHandlerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class StockExitExchangeService implements StockExitHandlerInterface
{
    protected function extractParams(Request $request): array
    {
        return [
            'person' => $request->input('person'),
            'user_id' => Auth::id(),
        ];
    }

    /**
     * @param Collection<int, Collection<\App\Models\StockExit>> $productExits
     */
    public function handle(Request $request, Collection $productExits): void
    {
        $exchangeArgs = $this->extractParams($request);
        $productExits->each(
            fn(Collection $exits) => $exits->each(
                fn($exit) => Exchange::create([
                    ...$exchangeArgs,
                    'stock_exit_id' => $exit->id
                ])
            )
        );
    }
}
