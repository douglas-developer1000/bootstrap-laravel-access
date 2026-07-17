<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Libraries\Enums\InvoiceStatusEnum;

interface LicenseStatusStateInterface
{
    public function changePlan(): void;

    public function activateLicense(): void;

    public function expireLicense(): void;

    public function cancelLicense(): void;

    public function abandonLicense(string $reason, InvoiceStatusEnum $invoiceStatus): void;
}
