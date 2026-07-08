<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

use Exception;

enum PlanNameEnum: string
{
    case MODULE_A = 'module-a';
    case MODULE_B = 'module-b';
    case MODULE_C = 'module-c';
    case MODULE_D = 'module-d';

    public function toString(): string
    {
        return match ($this) {
            self::MODULE_A => 'Módulo Simples',
            self::MODULE_B => 'Módulo Básico',
            self::MODULE_C => 'Módulo Médio',
            self::MODULE_D => 'Módulo Pro',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::MODULE_A => 'Adequado para quem quer apenas conhecer o sistema.',
            self::MODULE_B => 'Controle básico! Nada mais...',
            self::MODULE_C => 'Ideal para quem quer mais controle.',
            self::MODULE_D => 'Gerencie tudo o que for possível e mais um pouco...',
        };
    }
}
