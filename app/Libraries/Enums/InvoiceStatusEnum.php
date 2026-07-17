<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

enum InvoiceStatusEnum: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case VOIDED = 'voided';
    case EXPIRED = 'expired';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
}
