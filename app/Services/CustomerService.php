<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Enums\CustomerContactEnum;
use App\Libraries\Enums\CustomerPhoneTypeEnum;
use App\Libraries\Enums\DayPeriodsEnum;
use App\Repositories\CustomerPhoneRepository;
use App\Repositories\CustomerRepository;
use App\Models\Customer;

final class CustomerService
{
    public function __construct(
        protected CustomerRepository $customerRepo,
        protected CustomerPhoneRepository $customerPhoneRepo,
    ) {
        // ...
    }

    public function preparePersistence(Request $request)
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

    public function createCustomer(Request $request)
    {
        return $this->customerRepo->create(
            $this->preparePersistence($request)
        );
    }

    public function updateCustomer(Request $request, Customer $customer)
    {
        return $this->customerRepo->update(
            $customer->id,
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
            return;
        }
        $this->customerPhoneRepo->attachPhones(
            $customer,
            $phones->all()
        );
    }

    public function updatePhones(Request $request, Customer $customer)
    {
        $this->customerPhoneRepo->deleteByCustomer($customer);
        $this->createPhones($request, $customer);
    }

    public function getPhones(Customer $customer)
    {
        return $this->customerPhoneRepo->findByCustomer($customer);
    }
}
