<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class InvalidStatusException extends RuntimeException
{
    public static function licenseRelease(int $licenseId): self
    {
        return new self('Somente licenças ativas podem ser liberadas.');
    }

    public static function invoiceAnnulment(int $licenseId): self
    {
        return new self('Status não permitido para anulação de invoices');
    }
}
