<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sale\SaleRequest;
use App\Libraries\Enums\DiscountTypeEnum;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class SaleController extends Controller
{
    protected User $user;
    public function __construct(protected SaleService $svc)
    {
        $this->user = Auth::user();
    }
    public function index(Request $request)
    {
        return view('pages.sales.index', [
            'list' => $this->svc->prepareIndex($request),

            'models' => fn(LengthAwarePaginator $pagination) => (
                $this->svc->hydrateSales($pagination->all())
            ),
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function show(Sale $sale)
    {
        $exits = $sale->stockExits()->with(
            'stockEntry.product',
            fn(BelongsTo $query) => $query->select(['id', 'name'])
        )->with(
            'stockEntry.supplier',
            fn(BelongsTo $query) => $query->select(['id', 'name'])
        )->get();
        $payments = $sale->payments()->with(
            'paymentCards'
        )->get();
        // dd(json_encode($exits));

        return view('pages.sales.show', [
            'sale' => $sale,

            'discount' => $sale->discount,
            'parseDiscount' => fn(string $type, float|int $value) => (
                DiscountTypeEnum::parseDiscountValue($type, $value)
            ),
            'hasAccess' => $this->user->can(...),
            'exits' => $exits,
            'payments' => $payments,
            'parsePaymentValue' => fn(float|int $value) => (
                Number::currency(
                    number: $value,
                    in: 'BRL',
                    locale: 'pt_BR',
                    precision: 2
                )
            )
        ]);
    }

    public function destroy(Sale $sale)
    {
        $this->svc->removeSale($sale);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Venda removida com sucesso!'
        ]);
    }

    /**
     * @param Sale[] $saleList
     */
    public function destroyList(SaleRequest $request, string $key, array $saleList)
    {
        $this->svc->removeSaleGroup($saleList);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Vendas removidas com sucesso!'
        ]);
    }
}
