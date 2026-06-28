<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

use Carbon\CarbonInterface;
use Exception;

enum BillingPeriodEnum: string
{
    case NONE = 'none';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';

    public function advance(CarbonInterface $date): CarbonInterface
    {
        return match ($this) {
            self::WEEKLY => $date->copy()->addWeek(),
            self::MONTHLY => $date->copy()->addMonthNoOverflow(),
            self::QUARTERLY => $date->copy()->addMonths(3),
            self::YEARLY => $date->copy()->addYear(),
            default => throw new Exception('Tipo de faturamento inválido'),
        };
    }

    public function toString(): string
    {
        return match ($this) {
            self::WEEKLY => 'Semanal',
            self::MONTHLY => 'Mensal',
            self::QUARTERLY => 'Trimestral',
            self::YEARLY => 'Anual',
            self::NONE => 'Nenhum',
        };
    }
}
