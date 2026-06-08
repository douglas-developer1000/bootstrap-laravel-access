<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CustomerRequest;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct(protected CustomerService $svc)
    {
        // ...
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        return view('pages.customers.index', [
            'list' => $this->svc->prepareIndex($request),

            'models' => fn(LengthAwarePaginator $pagination) => (
                $this->svc->hydrateCustomer($pagination->all())
            ),
            'hasAccess' => $user->can(...),
        ]);
    }

    public function create()
    {
        return view('pages.customers.create');
    }

    public function show(Customer $customer)
    {
        return view('pages.customers.show', [
            'customer' => $customer,
            'phones' => $this->svc->getPhones($customer),
        ]);
    }

    public function edit(Customer $customer)
    {
        return view('pages.customers.edit', [
            'customer' => $customer,
            'phones' => $this->svc->getEditionPhones($customer),
        ]);
    }

    public function store(CustomerRequest $request)
    {
        $customer = $this->svc->createCustomer(
            $this->svc->extractCustomerParams($request)
        );
        $this->svc->createPhones($request, $customer);

        return redirect()->route('customers.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Cliente criado com sucesso!'
        ]);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $this->svc->updateCustomer(
            $this->svc->extractCustomerParams($request),
            $customer,
        );
        $this->svc->updatePhones($request, $customer);

        return redirect()->route('customers.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Cliente atualizado com sucesso!'
        ]);
    }

    public function destroy(Customer $customer)
    {
        $this->svc->removeCustomer($customer);

        return redirect()->route(
            'customers.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Cliente removido com sucesso!'
        ]);
    }

    /**
     * @param Customer[] $customerList
     */
    public function removeGroup(CustomerRequest $request, string $key, array $customerList)
    {
        $this->svc->removeCustomerList($customerList);

        return redirect()->route(
            'customers.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Clientes removidos com sucesso!'
        ]);
    }

    public function restore(Customer $customerDeleted)
    {
        $this->svc->restoreCustomer($customerDeleted);

        return redirect()->route(
            'customers.index',
            ['trashed' => 1]
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Cliente restaurado com sucesso!'
        ]);
    }

    public function restoreGroup(CustomerRequest $request, string $key, array $customerList)
    {
        $this->svc->restoreCustomerGroup($customerList);

        return redirect()->route(
            'payment-cards.index',
            ['trashed' => 1]
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Cartões restaurados com sucesso!'
        ]);
    }
}
