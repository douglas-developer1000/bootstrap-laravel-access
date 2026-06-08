<?php

declare(strict_types=1);

namespace App\Http\Requests\Discount\Strategies;

use App\Http\Requests\LateValidationInterface;
use App\Models\Discount;

final class Update extends Persistence
{
    public function __construct(LateValidationInterface $late, Discount $discount)
    {
        parent::__construct($late, $discount);
    }
}
