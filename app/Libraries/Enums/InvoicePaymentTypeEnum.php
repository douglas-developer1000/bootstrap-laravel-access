<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

enum InvoicePaymentTypeEnum: string
{
    case BANK_SLIP = 'bank_slip';
    case CREDIT_CARD = 'credit-card';
    case PIX = 'pix';
    case BANK_TRANSFER = 'bank-transfer';
    case MONEY = 'money';

    public function toString(): string
    {
        return match ($this) {
            self::BANK_SLIP => 'Boleto',
            self::CREDIT_CARD => 'Cartão de crédito',
            self::PIX => 'Pix',
            self::BANK_TRANSFER => 'Transferência bancária',
            self::MONEY => 'Dinheiro',
        };
    }
}
