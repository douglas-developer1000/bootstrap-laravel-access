<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategory\ProductCategoryRequest;
use App\Models\ProductCategory;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class ProductCategoryController extends Controller
{
    public function __construct(protected ProductCategoryService $svc)
    {
        // ...
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        return view('pages.products.categories.index', [
            'list' => $this->svc->prepareIndex($request),
            'models' => fn(LengthAwarePaginator $pagination) => (
                $this->svc->hydrateProductCategory($pagination->all())
            ),
            'hasAccess' => $user->can(...),
        ]);
    }

    public function create()
    {
        return view('pages.products.categories.create', [
            'categories' => $this->svc->getProductCategories([
                'id',
                'name'
            ])
        ]);
    }

    public function show(ProductCategory $category)
    {
        return view('pages.products.categories.show', [
            'cat' => $category,
            'supCats' => $this->svc->getAncestorCategoryNames($category),
            'subCats' => $category->children,
            'products' => $category->products()->get('products.name'),
        ]);
    }

    public function edit(ProductCategory $category)
    {
        $childIds = $this->svc->getChildIds($category);

        return view('pages.products.categories.edit', [
            'category' => $category,
            'categories' => $this->svc->getProductCategories(
                columns: [
                    'id',
                    'name'
                ],
                except: [$category->id, ...$childIds],
                category: $category
            )
        ]);
    }

    public function update(ProductCategoryRequest $request, ProductCategory $category)
    {
        $this->svc->updateCategory(
            $category->id,
            $this->svc->extractProductCategoryParams($request)
        );

        return redirect()->route('product-categories.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Categoria atualizada com sucesso!'
        ]);
    }

    public function store(ProductCategoryRequest $request)
    {
        $this->svc->createCategory(
            $this->svc->extractProductCategoryParams($request)
        );

        return redirect()->route('product-categories.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Categoria criada com sucesso!'
        ]);
    }

    public function destroy(ProductCategory $category)
    {
        $this->svc->removeProductCategory($category);

        return redirect()->route('product-categories.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Categoria removida com sucesso!'
        ]);
    }

    public function removeGroup(ProductCategoryRequest $request, string $key, array $productCategoryList)
    {
        $this->svc->removeProductCategoryList($productCategoryList);

        return redirect()->route('product-categories.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Categorias removidas com sucesso!'
        ]);
    }

    public function restore(ProductCategory $prodCategoryDeleted)
    {
        $this->svc->restoreProductCategory($prodCategoryDeleted);

        return redirect()->route(
            'product-categories.index',
            ['trashed' => 1]
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Categoria restaurada com sucesso!'
        ]);
    }

    public function restoreGroup(ProductCategoryRequest $request, string $key, array $productCategoryList)
    {
        $this->svc->restoreProductCategoryGroup($productCategoryList);

        return redirect()->route(
            'product-categories.index',
            ['trashed' => 1]
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Categorias restauradas com sucesso!'
        ]);
    }
}
