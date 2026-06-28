<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

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
}
