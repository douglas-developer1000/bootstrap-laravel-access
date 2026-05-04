<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Enums\CustomerContactEnum;
use App\Libraries\Enums\CustomerPhoneTypeEnum;
use App\Libraries\Enums\DayPeriodsEnum;
use App\Models\Customer;
use App\Models\CustomerPhone;
use Illuminate\Support\Collection;

final class CustomerService
{
    protected function preparePersistence(Request $request)
    {
        return collect([
            ...$request->only(['name', 'email', 'hostess', 'birthdate']),
            'user_id' => Auth::id(),
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

    public function createCustomer(Request $request): ?Customer
    {
        return Customer::create($this->preparePersistence($request));
    }

    public function updateCustomer(Request $request, Customer $customer): bool
    {
        return Customer::where(['id' => $customer->id])->update(
            $this->preparePersistence($request)
        );
    }

    public function createPhones(Request $request, Customer $customer)
    {
        $phones = collect(
            $request->only(
                CustomerPhoneTypeEnum::defineRequestBooleanEnumKeys('phone')
            )['phone'] ?? []
        )->filter(fn($value) => $value !== NULL)->map(fn($number, $key) => [
            'type' => CustomerPhoneTypeEnum::tryFrom($key),
            'number' => $number
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

    public function removeCustomer(int $id): void
    {
        Customer::where(['id' => $id])->delete();
    }

    public function removeCustomerList(array $ids): void
    {
        Customer::whereIn('id', $ids)->delete();
    }
}
