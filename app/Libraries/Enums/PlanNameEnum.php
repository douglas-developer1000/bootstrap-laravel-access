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
            self::MODULE_A => 'Módulo A',
            self::MODULE_B => 'Módulo B',
            self::MODULE_C => 'Módulo C',
            self::MODULE_D => 'Módulo D',
            default => throw new Exception('Tipo de Módulo inválido')
        };
    }
}
