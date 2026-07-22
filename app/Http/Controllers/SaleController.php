<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\ListStorager;
use App\Http\Requests\Sale\SaleRequest;
use App\Libraries\Enums\DiscountTypeEnum;
use App\Libraries\Enums\PaymentTypeEnum;
use App\Libraries\Enums\StockExitTypeEnum;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use App\Services\CustomerService;
use App\Services\SaleService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class SaleController extends Controller
{
    protected User $user;

    public function __construct(protected SaleService $svc)
    {
        $this->user = Auth::user();
    }

    public function index(Request $request, CustomerService $customerSvc)
    {
        return view('pages.sales.index', [
            'list' => $this->svc->prepareIndex($request),

            'models' => fn (LengthAwarePaginator $pagination) => (
                $this->svc->hydrateSales($pagination->all())
            ),
            'checkboxes' => collect($this->svc->definePayTypes())->mapWithKeys(
                fn (PaymentTypeEnum $payType) => [
                    $payType->value => $payType->toString(),
                ]
            )->all(),
            'hasAccess' => $this->user->can(...),

            'productsToExit' => collect(
                ListStorager::getList('productsToExit')
            ),

            'getAnonymousCustomer' => $customerSvc->anonymousCustomer(...),
            'makeSaleNoCustomerRoute' => fn (Customer $customer) => (
                route('stocks.exits.sale.create', [
                    'exitType' => StockExitTypeEnum::SALE->value,
                    'customer' => $customer->id,
                ])
            ),
        ]);
    }

    public function show(Sale $sale)
    {
        $exits = $sale->stockExits()->with(
            'stockEntry.product',
            fn (BelongsTo $query) => $query->select(['id', 'name'])
        )->with(
            'stockEntry.supplier',
            fn (BelongsTo $query) => $query->select(['id', 'name'])
        )->get();
        $payments = $sale->payments()->with(
            'paymentCards'
        )->get();

        return view('pages.sales.show', [
            'sale' => $sale,

            'discount' => $sale->discount,
            'parseDiscount' => fn (string $type, float|int $value) => (
                DiscountTypeEnum::parseDiscountValue($type, $value)
            ),
            'hasAccess' => $this->user->can(...),
            'exits' => $exits,
            'payments' => $payments,
        ]);
    }

    public function destroy(Sale $sale)
    {
        $this->svc->removeSale($sale);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Venda removida com sucesso!',
        ]);
    }

    /**
     * @param  Sale[]  $saleList
     */
    public function destroyList(SaleRequest $request, string $key, array $saleList)
    {
        $this->svc->removeSaleGroup($saleList);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Vendas removidas com sucesso!',
        ]);
    }
}
