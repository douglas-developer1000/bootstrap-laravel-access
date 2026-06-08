<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StockExit\StockExitRequest;
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

    public function removeExchange(StockExit $exit)
    {
        $this->svc->removeExchange($exit);
        $this->exitSvc->removeStockExit($exit);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Troca removida com sucesso!'
        ]);
    }

    public function removeExchangeGroup(StockExitRequest $request, string $key, array $stockExitList)
    {
        $this->svc->removeExchangeGroup($stockExitList);
        $this->exitSvc->removeStockExitGroup($stockExitList);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Trocas removidas com sucesso!'
        ]);
    }
}
