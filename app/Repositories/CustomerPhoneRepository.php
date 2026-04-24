<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Libraries\Enums\CustomerPhoneTypeEnum;
use App\Repositories\Contracts\AbstractRepository;
use App\Models\Customer;
use App\Models\CustomerPhone;

final class CustomerPhoneRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(CustomerPhone::class);
    }

    /**
     * Attach new CustomerPhone instances to customer
     *
     * @param Customer $customer
     * @param array<array{number: string, type: CustomerPhoneTypeEnum}> $phoneData
     * @return iterable<CustomerPhone>
     */
    public function attachPhones(Customer $customer, array $phoneData): array
    {
        return $customer->phones()->saveMany(
            collect($phoneData)->map(
                fn(array $row) => new CustomerPhone([
                    'type' => $row['type'],
                    'number' => $row['number'],
                ])
            )->all()
        );
    }

    public function findByCustomer(Customer $customer)
    {
        return $this->loadModel()::where([
            'customer_id' => $customer->id
        ])->get();
    }

    public function deleteByCustomer(Customer $customer)
    {
        $customer->phones()->delete();
    }
}
