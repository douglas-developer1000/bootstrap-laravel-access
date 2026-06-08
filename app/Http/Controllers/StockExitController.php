<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StockExit\StockExitRequest;
use App\Libraries\Enums\DiscountTypeEnum;
use App\Libraries\Enums\StockExitTypeEnum;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Services\CustomerService;
use App\Services\DiscountService;
use App\Services\PaymentCardService;
use App\Services\ProductToExitHandlerService;
use App\Services\StockEntryService;
use App\Services\StockExitExchangeService;
use App\Services\StockExitSaleService;
use App\Services\StockExitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

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
        StockExitSaleService $saleSvc,
        StockExitExchangeService $exchangeSvc,
        ProductToExitHandlerService $prodToExitSvc,
    ) {
        $type = StockExitTypeEnum::from($request->input('type'));

        $productExits = $this->svc->makeStockExits($this->svc->extractParams($request, $type));

        if ($type === StockExitTypeEnum::SALE) {
            $this->svc->handleStockExits($saleSvc, $request, $productExits);
        } elseif ($type === StockExitTypeEnum::EXCHANGE) {
            $this->svc->handleStockExits($exchangeSvc, $request, $productExits);
        }
        $prodToExitSvc->clearProductsToExit();

        return redirect()->route('stocks.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Estoque utilizado com sucesso!'
        ]);
    }

    public function markSale(ProductToExitHandlerService $svc, Product $product): RedirectResponse
    {
        if (!collect($svc->getProductsToExit())->contains($product->id)) {
            session()->push('productsToExit', $product->id);
        }

        return redirect()->route('stocks.index');
    }

    public function unmarkSale(ProductToExitHandlerService $svc, Product $product): RedirectResponse
    {
        $productToExit = collect($svc->getProductsToExit());
        $newProductToExit = $productToExit->reject(fn(int $id) => $id === $product->id);
        session()->put('productsToExit', $newProductToExit);

        if ($newProductToExit->isEmpty()) {
            return redirect()->route('stocks.index');
        }
        return redirect()->back();
    }

    public function createSaleExit(
        ProductToExitHandlerService $svc,
        PaymentCardService $payCardSvc,
        DiscountService $discountSvc,
        StockExitTypeEnum $exitType,
        Customer $customer,
    ) {
        $products = Product::findMany($svc->getProductsToExit());

        return view('pages.stocks.exits.create', [
            'exitType' => $exitType,
            'products' => $products,
            'cards' => $payCardSvc->getPaymentCards(),
            'entries' => $products->mapWithKeys(fn(Product $product) => [
                $product->id => $this->entrySvc->getRemainStockEntries($product)
            ]),
            'discounts' => $discountSvc->getAllDiscounts(),
            'parseDiscount' => fn(string $type, float|int $value) => (
                DiscountTypeEnum::parseDiscountValue($type, $value)
            ),
            'customer' => $customer,
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function createExit(
        ProductToExitHandlerService $svc,
        StockExitTypeEnum $exitType
    ) {
        $products = Product::findMany($svc->getProductsToExit());

        return view('pages.stocks.exits.create', [
            'exitType' => $exitType,
            'products' => $products,
            'entries' => $products->mapWithKeys(fn(Product $product) => [
                $product->id => $this->entrySvc->getRemainStockEntries($product)
            ]),
            'hasAccess' => $this->user->can(...),
        ]);
    }
}
