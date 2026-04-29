<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CustomerRequest;
use App\Libraries\Enums\CustomerPhoneTypeEnum;
use Illuminate\Http\Request;
use App\Libraries\Utils\Paginator;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct(protected CustomerService $svc)
    {
        // ...
    }

    public function index(Request $request)
    {
        $fields = ['id', 'name', 'email', 'created_at'];
        $group = Paginator::buildGroup($request->only('group'));
        $searchName = Paginator::buildSearch($request->only('name'), 'name');
        $sort = Paginator::buildSort($request->only('sort'), $fields);
        $order = Paginator::buildOrder($request->only('order'));

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

    public function store(CustomerRequest $request)
    {
        $customer = $this->svc->createCustomer($request);
        $this->svc->createPhones($request, $customer);

        return redirect()->route('customers.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Cliente criado com sucesso!'
        ]);
    }

    public function show(Customer $customer)
    {
        $phones = $this->svc->getPhones($customer);

        return view('pages.customers.show', [
            'customer' => $customer,
            'phones' => $phones,
        ]);
    }

    public function edit(Customer $customer)
    {
        /** @var Collection<string, string> $phonesStored */
        $phonesStored = $this->svc->getPhones($customer)->mapWithKeys(
            fn($phone) => [$phone->type->value => $phone->number]
        );

        $phones = collect(
            CustomerPhoneTypeEnum::casesExcept(CustomerPhoneTypeEnum::OTHER)
        )->mapWithKeys(
            fn($enum) => [$enum->value => $phonesStored->get($enum->value, '')]
        );

        return view('pages.customers.edit', [
            'customer' => $customer,
            'phones' => $phones,
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

    public function destroy(Customer $customer)
    {
        $customer->delete();

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
        $remotions = collect($request->validated('remotion'))->map(
            fn($val) => \intval($val)
        )->all();
        $this->svc->removeList($remotions);

        return redirect()->route(
            'customers.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Clientes removidos com sucesso!'
        ]);
    }
}
