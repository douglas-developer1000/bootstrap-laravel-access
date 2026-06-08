<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

enum StockExitTypeEnum: string
{
    case SALE = 'sale';
    case EXCHANGE = 'exchange';
    case DEMONSTRATION = 'demonstration';
    case PERSONAL_USE = 'personal-use';
    case LOSS = 'loss';

    public function toString(): string
    {
        return match ($this) {
            self::EXCHANGE => 'Troca',
            self::SALE => 'Venda',
            self::DEMONSTRATION => 'Demonstração',
            self::PERSONAL_USE => 'Uso pessoal',
            self::LOSS => 'Descarte',
            default => throw new \Exception("Saída inválida de estoque", 1)
        };
    }
}
