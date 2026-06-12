<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\PhoneCast;
use App\Libraries\Enums\CustomerPhoneTypeEnum;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

/**
 * @property int $id
 * @property CustomerPhoneTypeEnum $type
 * @property \App\Libraries\Values\PhoneValue $number
 * @property int $customer_id
 * @property \Illuminate\Support\Carbon $created_at
 */
#[Fillable(['type', 'customer_id', 'number', 'created_at'])]
final class CustomerPhone extends Model
{
    public const UPDATED_AT = NULL;

    protected $table = 'customer_phone';

    protected $casts = [
        'type' => CustomerPhoneTypeEnum::class,
        'number' => PhoneCast::class
    ];
}
