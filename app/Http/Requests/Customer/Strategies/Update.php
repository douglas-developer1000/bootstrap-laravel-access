<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer\Strategies;

use App\Models\Customer;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class Update extends Persistence
{

    public function __construct(protected Customer $customer)
    {
        parent::__construct();
    }

    public function rules(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return [
            ...parent::rules(),
            'email' => [
                'bail',
                'nullable',
                'email',
                "max:{$this->emailMaxSize}",
                Rule::unique('customers', 'email')->ignore(
                    $this->customer->id,
                    'id'
                )->where(fn(Builder $query) => (
                    $query->where('user_id', $user->id)
                )),
            ]
        ];
    }
}
