<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\SupplierRequest;
use App\Models\Supplier;
use App\Models\User;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class SupplierController extends Controller
{
    protected User $user;

    public function __construct(protected SupplierService $svc)
    {
        /** @var User $user */
        $this->user = Auth::user();
    }

    public function index(Request $request)
    {
        return view('pages.suppliers.index', [
            'list' => $this->svc->prepareIndex($request),
            'models' => fn (LengthAwarePaginator $pagination) => (
                $this->svc->hydrateSupplier($pagination->all())
            ),
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function show(Supplier $supplier)
    {
        return view('pages.suppliers.show', [
            'supplier' => $supplier,
            'products' => $this->svc->findSupplierProducts($supplier),
        ]);
    }

    public function create()
    {
        return view('pages.suppliers.create', [
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function store(SupplierRequest $request)
    {
        $args = $this->svc->extractSupplierParams($request);
        $this->svc->createSupplier($args);

        return redirect()->route('suppliers.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Fornecedor criado com sucesso!',
        ]);
    }

    public function destroy(Supplier $supplier)
    {
        $this->svc->removeSupplier($supplier);

        return redirect()->route('suppliers.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Fornecedor removido com sucesso!',
        ]);
    }

    public function edit(Supplier $supplier)
    {
        return view('pages.suppliers.create', [
            'supplier' => $supplier,
            'hasAccess' => $this->user->can(...),

            'title' => 'Editar Fornecedor',
            'action' => route('suppliers.update', $supplier->id),
            'method' => 'PUT',
        ]);
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $params = $this->svc->extractSupplierParams($request, $supplier);
        $this->svc->updateSupplier($params, $supplier);

        return redirect()->route('suppliers.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Fornecedor atualizado com sucesso!',
        ]);
    }

    /**
     * @param  Supplier[]  $supplierList
     */
    public function removeGroup(SupplierRequest $request, string $key, array $supplierList)
    {
        $this->svc->removeSupplierGroup($supplierList);

        return redirect()->route('suppliers.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Fornecedores removidos com sucesso!',
        ]);
    }

    public function restore(Supplier $supplierDeleted)
    {
        $this->svc->restoreSupplier($supplierDeleted);

        return redirect()->route(
            'suppliers.index',
            ['trashed' => 1]
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Fornecedor restaurado com sucesso!',
        ]);
    }

    /**
     * @param  Supplier[]  $supplierList
     */
    public function restoreGroup(SupplierRequest $request, string $key, array $supplierList)
    {
        $this->svc->restoreSupplierGroup($supplierList);

        return redirect()->route(
            'suppliers.index',
            ['trashed' => 1]
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Fornecedores restaurados com sucesso!',
        ]);
    }
}
