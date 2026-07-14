<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StockEntry\StockEntryRequest;
use App\Libraries\Enums\DiscountTypeEnum;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
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
        /** @var User $user */
        $user = Auth::user();
        $suppliers = $user->cannot('viewAny', Supplier::class) ? [] : Supplier::notAnonymous()->get([
            'id',
            'name',
        ]);
        $discounts = $user->cannot('viewAny', Discount::class) ? [] : $discountSvc->getAllDiscounts();

        return view('pages.stocks.entries.create', [
            'product' => $product,
            'discounts' => $discounts,
            'parseDiscount' => fn (DiscountTypeEnum $type, float|int $value) => (
                DiscountTypeEnum::parseDiscountValue($type, $value)
            ),
            'suppliers' => $suppliers,
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
            'toastMsg' => 'Estoque criado com sucesso!',
        ]);
    }
}
