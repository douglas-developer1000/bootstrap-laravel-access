<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StockExit\StockExitRequest;
use App\Models\StockExit;
use App\Models\User;
use App\Services\RawExitService;
use App\Services\StockExitService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class RawExitController extends Controller
{
    protected User $user;
    public function __construct(
        protected RawExitService $svc,
        protected StockExitService $exitSvc,
    ) {
        /** @var User $user */
        $this->user = Auth::user();
    }
    public function index(Request $request)
    {
        return view('pages.raw-exits.index', [
            'list' => $this->svc->prepareIndex($request),

            'models' => fn(LengthAwarePaginator $pagination) => (
                $this->svc->hydrateStockExit($pagination->all())
            ),
            'hasAccess' => $this->user->can(...),
        ]);
    }
    public function destroy(StockExit $exit)
    {
        $this->exitSvc->removeStockExit($exit);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Saída removida com sucesso!'
        ]);
    }
    public function destroyGroup(StockExitRequest $request, string $key, array $stockExitList)
    {
        $this->exitSvc->removeStockExitGroup($stockExitList);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Saídas removidas com sucesso!'
        ]);
    }
}
