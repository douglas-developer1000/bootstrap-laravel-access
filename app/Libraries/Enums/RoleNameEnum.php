<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

use Exception;

enum RoleNameEnum: string
{
    case SUPER_ADMIN = 'super-admin';
    case USER = 'user';
    case FOR_SETTINGS = 'for-settings';
    case FOR_CUSTOMER = 'for-customer';
    case FOR_PRODUCT = 'for-product';
    case FOR_STOCK_ENTRY = 'for-stock-entry';
    case FOR_RAW_EXIT = 'for-raw-exit';
    case FOR_SALE_EXIT = 'for-sale-exit';
    case FOR_SUPPLIER = 'for-supplier';
    case FOR_DISCOUNT = 'for-discount';
    case FOR_EXCHANGE = 'for-exchange';
    case FOR_PAYMENT_CARD = 'for-payment-card';
    case FOR_LOSS_EXIT = 'for-loss-exit';
    case FOR_PERSONAL_USE_EXIT = 'for-personal-use-exit';
    case FOR_DEMONSTRATION_EXIT = 'for-demonstration-exit';

    public function description(): ?string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Controla tudo',
            self::FOR_SETTINGS => 'Acessar configurações',
            self::FOR_CUSTOMER => 'Cadastro de clientes',
            self::FOR_PRODUCT => 'Cadastro de produtos',
            self::FOR_STOCK_ENTRY => 'Entrada de estoque',
            self::FOR_RAW_EXIT => 'Saída de estoque (simples)',
            self::FOR_SALE_EXIT => 'Saída de estoque (venda)',
            self::FOR_SUPPLIER => 'Cadastro de fornecedor',
            self::FOR_DISCOUNT => 'Controle de descontos',
            self::FOR_EXCHANGE => 'Controle de trocas',
            self::FOR_PAYMENT_CARD => 'Cartões de crédito',
            self::FOR_LOSS_EXIT => 'Saída como descarte',
            self::FOR_PERSONAL_USE_EXIT => 'Saída como uso pessoal',
            self::FOR_DEMONSTRATION_EXIT => 'Saída como demonstração',
            default => NULL
        };
    }
}
