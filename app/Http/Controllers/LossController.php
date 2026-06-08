<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StockExit\StockExitRequest;
use App\Models\StockExit;
use App\Models\User;
use App\Services\LossService;
use App\Services\StockExitService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class LossController extends Controller
{
    protected User $user;

    public function __construct(
        protected LossService $svc,
        protected StockExitService $exitSvc,
    ) {
        /** @var User $user */
        $this->user = Auth::user();
    }

    public function index(Request $request)
    {
        return view('pages.losses.index', [
            'list' => $this->svc->prepareIndex($request),

            'models' => fn(LengthAwarePaginator $pagination) => (
                $this->svc->hydrateStockExit($pagination->all())
            ),
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function removeLoss(StockExit $exit)
    {
        $this->exitSvc->removeStockExit($exit);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Perda removida com sucesso!'
        ]);
    }

    /**
     * @param StockExit[] $stockExitList
     */
    public function removeLossGroup(StockExitRequest $request, string $key, array $stockExitList)
    {
        $this->exitSvc->removeStockExitGroup($stockExitList);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Perdas removidas com sucesso!'
        ]);
    }
}
