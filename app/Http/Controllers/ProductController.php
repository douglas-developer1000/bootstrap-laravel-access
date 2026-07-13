<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;

final class ProductController extends Controller
{
    public function __construct(
        protected ProductCategoryService $catSvc,
        protected ProductService $svc,
    ) {
        // ...
    }

    public function create()
    {
        $categories = $this->catSvc->getProductCategories(['id', 'name']);
        if ($categories->isEmpty()) {
            return redirect()->route(
                'product-categories.create'
            )->with('emptyCategories', true);
        }

        return view('pages.products.create', [
            'categories' => $categories,
        ]);
    }

    public function edit(Product $product)
    {
        return view('pages.products.create', [
            'product' => $product,
            'categories' => $this->catSvc->getProductCategories(['id', 'name']),
            'title' => 'Editar Produto',
            'action' => route('products.update', $product->id),
            'method' => 'PUT',
            'btnText' => 'Editar',
        ]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->svc->updateProduct(
            $this->svc->extractProductParams($request),
            $product
        );

        return redirect()->route('stocks.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Produto atualizado com sucesso!',
        ]);
    }

    public function store(ProductRequest $request)
    {
        $this->svc->createProduct(
            $this->svc->extractProductParams($request)
        );

        return redirect()->route('stocks.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Produto cadastrado com sucesso!',
        ]);
    }

    public function destroy(Product $product)
    {
        $this->svc->removeProduct($product);

        return redirect()->route('stocks.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Produto removido com sucesso!',
        ]);
    }

    /**
     * @param  Product[]  $productList
     */
    public function removeGroup(ProductRequest $request, string $key, array $productList)
    {
        $this->svc->removeProductList($productList);

        return redirect()->route('stocks.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Produtos removidos com sucesso!',
        ]);
    }

    public function restore(Product $productDeleted)
    {
        $this->svc->restoreProduct($productDeleted);

        return redirect()->route('stocks.index', [
            'trashed' => 1,
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Produtos restaurados com sucesso!',
        ]);
    }

    /**
     * Restore a soft-deleted product list
     *
     * @param  Product[]  $productList
     * @return RedirectResponse
     */
    public function restoreGroup(ProductRequest $request, string $key, array $productList)
    {
        $this->svc->restoreProductGroup($productList);

        return redirect()->route('stocks.index', [
            'trashed' => '1',
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Produtos restaurados com sucesso!',
        ]);
    }
}
