<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Services\ProductCategoryService;
use App\Services\StockEntryService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class StockController extends Controller
{
    protected User $user;

    public function __construct(
        protected StockService $svc,

        protected ProductCategoryService $catSvc,
        protected StockEntryService $stockEntrySvc,
    ) {
        $this->user = Auth::user();
    }

    public function index(Request $request)
    {
        $trashed = $request->boolean('trashed');

        return view('pages.stocks.index', [
            'list' => $this->svc->prepareIndex($request),

            'models' => fn (LengthAwarePaginator $pagination) => (
                $this->svc->hydrateStocks($pagination->all())
            ),
            'hasAccess' => $this->user->can(...),

            'trashed' => $trashed,
            'subject' => $trashed ? 'Produtos removidos' : 'Estoque',
        ]);
    }

    /**
     * Display:
     *
     * - product's name
     * - categories (sub e sup)
     * - total quantity sum available into stock
     * - suppliers
     *     + stock quantity
     * - stock entries validity (ordered from lower to greater)
     */
    protected function showStockData(Product $product, bool $deleted)
    {
        $data = $this->stockEntrySvc->getProductStockEntries($product, $deleted);

        return view('pages.stocks.show', [
            'product' => $product,
            'entries' => $data['entries'],
            'emptyStock' => $data['emptyStock'],
            'categories' => $this->catSvc->getAncestorCategoryNames(
                $product->product_category_id
            ),
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function show(Product $product)
    {
        return $this->showStockData($product, false);
    }

    /**
     * Display stock data from removed product
     *
     * @see self::show
     */
    public function showDeleted(Product $productDeleted)
    {
        return $this->showStockData($productDeleted, true);
    }
}
