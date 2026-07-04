<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class LicenseStatusModificationException extends RuntimeException
{
    public static function planModification(int $licenseId): self
    {
        return new self('Somente licenças ativas podem realizar a transição de plano.');
    }

    public static function activation(int $licenseId): self
    {
        return new self('Somente licenças pendentes podem ser ativadas.');
    }

    public static function reactivation(int $licenseId): self
    {
        return new self('Licenças não-ativas e não-canceladas ou fora da expiração não pode ser reativadas.');
    }

    public static function expiration(int $licenseId): self
    {
        return new self('Somente licenças ativas, não-canceladas e com expiração vencida podem ser expiradas.');
    }

    public static function cancelation(int $licenseId): self
    {
        return new self('Somente licenças ativas ou com pré-cancelamento, mais a expiração, permitem operações de cancelamento.');
    }

    public static function abandonment(int $licenseId): self
    {
        return new self('Somente licenças pendentes podem ser abandonadas.');
    }
}
