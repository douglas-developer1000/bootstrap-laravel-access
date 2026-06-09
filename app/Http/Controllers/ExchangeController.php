<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StockExit\StockExitRequest;
use App\Models\Exchange;
use App\Models\StockExit;
use App\Models\User;
use App\Services\ExchangeService;
use App\Services\StockExitService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class ExchangeController extends Controller
{
    protected User $user;

    public function __construct(
        protected ExchangeService $svc,
        protected StockExitService $exitSvc,
    ) {
        /** @var User $user */
        $this->user = Auth::user();
    }

    public function index(Request $request)
    {
        return view('pages.exchanges.index', [
            'list' => $this->svc->prepareIndex($request),

            'models' => fn(LengthAwarePaginator $pagination) => (
                $this->svc->hydrateExchange($pagination->all())
            ),
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function destroy(Exchange $exchange, StockExit $exit)
    {
        $this->svc->removeExchange($exchange);
        $this->exitSvc->removeStockExit($exit);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Troca removida com sucesso!'
        ]);
    }

    public function destroyGroup(StockExitRequest $request, string $key, array $exchangeList)
    {
        $exits = $this->svc->removeExchangeGroup($exchangeList);
        $this->exitSvc->removeStockExitGroup($exits);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Trocas removidas com sucesso!'
        ]);
    }
}
