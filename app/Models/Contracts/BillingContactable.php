<?php

declare(strict_types=1);

namespace App\Models\Contracts;

interface BillingContactable
{
    public function getBillingEmail(): string;
}
