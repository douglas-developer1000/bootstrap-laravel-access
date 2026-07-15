<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StockExit\StockExitRequest;
use App\Libraries\Enums\StockExitTypeEnum;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\PaymentCard;
use App\Models\Product;
use App\Models\User;
use App\Services\DiscountService;
use App\Services\ListSelectorService;
use App\Services\PaymentCardService;
use App\Services\SaleService;
use App\Services\StockEntryService;
use App\Services\StockExitExchangeService;
use App\Services\StockExitSaleService;
use App\Services\StockExitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class StockExitController extends Controller
{
    protected User $user;

    public function __construct(
        protected StockEntryService $entrySvc,
        protected StockExitService $svc,
    ) {
        $this->user = Auth::user();
    }

    public function storeExit(
        StockExitRequest $request,
        StockExitSaleService $stockExitSaleSvc,
        StockExitExchangeService $exchangeSvc,
        ListSelectorService $listSelector,
        StockExitTypeEnum $exitType,
    ) {
        DB::transaction(function () use ($request, $exitType, $stockExitSaleSvc, $exchangeSvc) {
            $productExits = $this->svc->makeStockExits($this->svc->extractParams($request, $exitType));

            if ($exitType === StockExitTypeEnum::SALE) {
                $this->svc->handleStockExits($stockExitSaleSvc, $request, $productExits);
            } elseif ($exitType === StockExitTypeEnum::EXCHANGE) {
                $this->svc->handleStockExits($exchangeSvc, $request, $productExits);
            }
        });

        $listSelector->clearList('productsToExit');

        return redirect()->route('stocks.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Estoque utilizado com sucesso!',
        ]);
    }

    public function markSale(ListSelectorService $svc, Product $product): RedirectResponse
    {
        $svc->store('productsToExit', $product->id);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => "Produto {$product->name} marcado com sucesso!",
        ]);
    }

    public function unmarkSale(ListSelectorService $svc, Product $product): RedirectResponse
    {
        $svc->unstore('productsToExit', $product->id);
        if (collect($svc->getList('productsToExit'))->isEmpty()) {
            return redirect()->route('stocks.index')->with([
                'toastShow' => true,
                'toastMsg' => "Produto {$product->name} desmarcado com sucesso!",
            ]);
        }

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => "Produto {$product->name} desmarcado com sucesso!",
        ]);
    }

    public function createSaleExit(
        ListSelectorService $svc,
        PaymentCardService $payCardSvc,
        DiscountService $discountSvc,
        SaleService $saleSvc,
        StockExitTypeEnum $exitType,
        Customer $customer,
    ) {
        $products = Product::findMany($svc->getList('productsToExit'));
        $cards = $this->user->can(
            'viewAny',
            PaymentCard::class
        ) ? $payCardSvc->getPaymentCards() : [];
        $discounts = $this->user->can(
            'viewAny',
            Discount::class
        ) ? $discountSvc->getAllDiscounts() : [];

        return view('pages.stocks.exits.create', [
            'exitType' => $exitType,
            'products' => $products,
            'payTypes' => $saleSvc->definePayTypes(),

            'cards' => $cards,
            'discounts' => $discounts,

            'entries' => $products->mapWithKeys(fn (Product $product) => [
                $product->id => $this->entrySvc->getRemainStockEntries($product),
            ]),
            'customer' => $customer,
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function createExit(
        ListSelectorService $svc,
        StockExitTypeEnum $exitType
    ) {
        $products = Product::findMany($svc->getList('productsToExit'));

        return view('pages.stocks.exits.create', [
            'exitType' => $exitType,
            'products' => $products,
            'entries' => $products->mapWithKeys(fn (Product $product) => [
                $product->id => $this->entrySvc->getRemainStockEntries($product),
            ]),
            'hasAccess' => $this->user->can(...),
        ]);
    }
}
