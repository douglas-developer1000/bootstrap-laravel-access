<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CustomerRequest;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\PaginatorService;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct(protected CustomerService $svc)
    {
        // ...
    }

    public function index(Request $request, PaginatorService $paginator)
    {
        $fields = ['id', 'name', 'email', 'created_at'];
        $group = $paginator->buildGroup($request->only('group'));
        $searchName = $paginator->buildSearch($request->only('name'), 'name');
        $sort = $paginator->buildSort($request->only('sort'), $fields);
        $order = $paginator->buildOrder($request->only('order'));

        $query = Customer::where('user_id', Auth::id());
        if ($searchName) {
            $searchName = addcslashes($searchName, '%_');
            $query = $query->whereLike('name', "%{$searchName}%");
        }
        $list = $query->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: $fields
        );

        return view('pages.customers.index', ['list' => $list]);
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
        $customer = $this->svc->createCustomer($request);
        $this->svc->createPhones($request, $customer);

        return redirect()->route('customers.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Cliente criado com sucesso!'
        ]);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $this->svc->updateCustomer($request, $customer);
        $this->svc->updatePhones($request, $customer);

        return redirect()->route('customers.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Cliente atualizado com sucesso!'
        ]);
    }

    public function destroy(int $id)
    {
        $this->svc->removeCustomer($id);

        return redirect()->route(
            'customers.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Cliente removido com sucesso!'
        ]);
    }

    public function removeGroup(CustomerRequest $request)
    {
        $this->svc->removeCustomerList($request->validated('remotion'));

        return redirect()->route(
            'customers.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Clientes removidos com sucesso!'
        ]);
    }
}
