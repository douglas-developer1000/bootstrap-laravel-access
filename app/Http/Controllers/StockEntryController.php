<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StockEntry\StockEntryRequest;
use App\Libraries\Enums\DiscountTypeEnum;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\DiscountService;
use App\Services\StockEntryService;
use Illuminate\Support\Facades\Auth;

final class StockEntryController extends Controller
{
    public function __construct(protected StockEntryService $svc)
    {
        // ...
    }
    public function createEntry(DiscountService $discountSvc, Product $product)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('pages.stocks.entries.create', [
            'product' => $product,
            'discounts' => $discountSvc->getAllDiscounts(),
            'parseDiscount' => fn(string $type, float|int $value) => (
                DiscountTypeEnum::parseDiscountValue($type, $value)
            ),
            'suppliers' => Supplier::all([
                'id',
                'name'
            ]),
            'hasAccess' => $user->can(...),
        ]);
    }

    public function storeEntry(StockEntryRequest $request, Product $product)
    {
        $this->svc->createStockEntry(
            $this->svc->extractStockEntryParams($request, $product)
        );

        return redirect()->route('stocks.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Estoque criado com sucesso!'
        ]);
    }
}
