<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

enum PermissionNameEnum: string
{
    case HEADER_SETTINGS = 'header-settings';
    case CUSTOMER_INDEX = 'customer-index';
    case CUSTOMER_CREATE = 'customer-create';
    case CUSTOMER_STORE = 'customer-store';
    case CUSTOMER_SHOW = 'customer-show';
    case CUSTOMER_EDIT = 'customer-edit';
    case CUSTOMER_UPDATE = 'customer-update';
    case CUSTOMER_DESTROY = 'customer-destroy';
}
