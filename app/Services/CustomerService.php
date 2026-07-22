<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Paginator;
use App\Libraries\Enums\CustomerContactEnum;
use App\Libraries\Enums\CustomerPhoneTypeEnum;
use App\Libraries\Enums\DayPeriodsEnum;
use App\Libraries\Values\PhoneValue;
use App\Models\Customer;
use App\Models\CustomerPhone;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Override;

final class CustomerService
{
    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class() extends AbstractPaginatorIndex
        {
            #[Override]
            public function query(Request $request): Builder
            {
                $trashed = $request->boolean('trashed');

                return Customer::whereBelongsTo(Auth::user())->getQuery()
                    ->when(
                        $trashed,
                        fn (Builder $query) => $query->whereNotNull('deleted_at')
                    )
                    ->when(
                        ! $trashed,
                        fn (Builder $query) => $query->whereNull('deleted_at')
                    );
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                return parent::attachQuery($request, $query)
                    ->when(
                        Paginator::buildSearch($request->only('name'), 'name'),
                        function (Builder $query, string $searchName) {
                            $searchName = addcslashes($searchName, '%_');

                            return $query->whereLike(
                                'name',
                                "%{$searchName}%"
                            );
                        }
                    );
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
            ) ?: null,
            'schedule' => DayPeriodsEnum::wrapRequestBooleanEnum(
                $request,
                'period'
            ) ?: null,
        ])->filter(fn ($value) => $value !== null)->all();
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
        )->filter(fn ($value) => $value !== null)->map(fn ($number, $key) => [
            'type' => CustomerPhoneTypeEnum::tryFrom($key),
            'number' => new PhoneValue($number),
        ])->values();

        if ($phones->isEmpty()) {
            return [];
        }

        return $customer->phones()->saveMany(
            $phones->map(
                fn (array $row) => new CustomerPhone([
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
            'customer_id' => $customer->id,
        ])->get();
    }

    /**
     * Return the phone collection with the stored phones from database
     */
    public function getEditionPhones(Customer $customer): Collection
    {
        /** @var Collection<string, string> $phonesStored */
        $phonesStored = $this->getPhones($customer)->mapWithKeys(
            fn ($phone) => [$phone->type->value => $phone->number]
        );

        return collect(
            CustomerPhoneTypeEnum::casesExcept(CustomerPhoneTypeEnum::OTHER)
        )->mapWithKeys(
            fn ($enum) => [$enum->value => $phonesStored->get($enum->value) ?? '']
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
     * @param  Customer[]  $customers
     */
    public function removeCustomerList(array $customers): void
    {
        collect($customers)->each($this->removeCustomer(...));
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

    public function anonymousCustomer(): Customer
    {
        return Customer::firstOrCreate(Customer::getAnonymousFields());
    }
}
