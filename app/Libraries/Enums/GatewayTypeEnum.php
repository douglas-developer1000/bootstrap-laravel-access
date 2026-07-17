<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

enum GatewayTypeEnum: string
{
    case STRIPE = 'stripe';
    case ASAAS = 'asaas';
}
