<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Enums\CustomerContactEnum;
use App\Libraries\Enums\CustomerPhoneTypeEnum;
use App\Libraries\Enums\DayPeriodsEnum;
use App\Libraries\Values\PhoneValue;
use App\Models\Customer;
use App\Models\CustomerPhone;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Override;
use Closure;

final class CustomerService
{
    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class extends AbstractPaginatorIndex
        {
            #[Override]
            public function query(Request $request): Builder
            {
                /** @var int|string $id */
                $id = Auth::id();

                $trashed = $request->boolean('trashed');
                if ($trashed) {
                    return Customer::where(
                        ['user_id' => $id]
                    )->whereNotNull('deleted_at')->getQuery();
                }
                return Customer::where(
                    ['user_id' => $id]
                )->whereNull('deleted_at')->getQuery();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                $searchName = $this->paginator->buildSearch($request->only('name'), 'name');
                if ($searchName) {
                    $searchName = addcslashes($searchName, '%_');
                    return parent::attachQuery($request, $query)->whereLike(
                        'name',
                        "%{$searchName}%"
                    );
                }
                return parent::attachQuery($request, $query);
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'name', 'email'];
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    public function extractCustomerParams(Request $request): array
    {
        /** @var int|string $id */
        $id = Auth::id();

        return collect([
            ...$request->only(['name', 'email', 'hostess', 'birthdate']),
            'user_id' => $id,
            'contact' => CustomerContactEnum::wrapRequestBooleanEnum(
                $request,
                'contact'
            ) ?: NULL,
            'schedule' => DayPeriodsEnum::wrapRequestBooleanEnum(
                $request,
                'period'
            ) ?: NULL,
        ])->filter(fn($value) => $value !== NULL)->all();
    }

    public function createCustomer(array $params): ?Customer
    {
        return Customer::create($params);
    }

    public function updateCustomer(array $params, Customer $customer)
    {
        return $customer->update($params);
    }

    public function createPhones(Request $request, Customer $customer)
    {
        $phones = collect(
            $request->only(
                CustomerPhoneTypeEnum::defineRequestBooleanEnumKeys('phone')
            )['phone'] ?? []
        )->filter(fn($value) => $value !== NULL)->map(fn($number, $key) => [
            'type' => CustomerPhoneTypeEnum::tryFrom($key),
            'number' => new PhoneValue($number)
        ])->values();

        if ($phones->isEmpty()) {
            return [];
        }
        return $customer->phones()->saveMany(
            $phones->map(
                fn(array $row) => new CustomerPhone([
                    'type' => $row['type'],
                    'number' => $row['number'],
                ])
            )->all()
        );
    }

    public function updatePhones(Request $request, Customer $customer)
    {
        $customer->phones()->delete();
        return $this->createPhones($request, $customer);
    }

    public function getPhones(Customer $customer): Collection
    {
        return CustomerPhone::where([
            'customer_id' => $customer->id
        ])->get();
    }

    /**
     * Return the phone collection with the stored phones from database
     */
    public function getEditionPhones(Customer $customer): Collection
    {
        /** @var Collection<string, string> $phonesStored */
        $phonesStored = $this->getPhones($customer)->mapWithKeys(
            fn($phone) => [$phone->type->value => $phone->number]
        );

        return collect(
            CustomerPhoneTypeEnum::casesExcept(CustomerPhoneTypeEnum::OTHER)
        )->mapWithKeys(
            fn($enum) => [$enum->value => $phonesStored->get($enum->value, '')]
        );
    }

    public function removeCustomer(Customer $customer): void
    {
        if ($customer->payments()->count() > 0) {
            $customer->delete();
        } else {
            $customer->forceDelete();
        }
    }

    /**
     * @param Customer[] $customers
     */
    public function removeCustomerList(array $customers): void
    {
        collect($customers)->each($this->removeCustomer(...));
    }

    /**
     * @param Closure(Builder): Builder $callback
     * @return \Illuminate\Database\Eloquent\Collection<int, Customer>
     */
    public function getAllCustomer(?Closure $callback = NULL)
    {
        /** @var int|string $id */
        $id = Auth::id();

        $query = Customer::where([
            'user_id' => $id
        ]);
        if ($callback) {
            $query = $callback($query->getQuery());
        }
        return $query->get();
    }

    public function restoreCustomer(Customer $customer)
    {
        $customer->restore();
    }

    public function restoreCustomerGroup(array $customers): void
    {
        collect($customers)->each($this->restoreCustomer(...));
    }

    public function hydrateCustomer(array $customers): Collection
    {
        return Customer::hydrate($customers);
    }
}
