<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

use App\Libraries\Traits\StorableEnumTrait;

enum CustomerContactEnum: string
{
    use StorableEnumTrait;

    case CHAT = 'chat';
    case PHONE = 'phone';
    case EMAIL = 'email';

    public function toString(): string
    {
        return match ($this) {
            self::CHAT => 'Chat',
            self::PHONE => 'Telefone',
            self::EMAIL => 'E-mail',
            default => throw new \Exception("tipo de contato inválido", 1)
        };
    }
}
