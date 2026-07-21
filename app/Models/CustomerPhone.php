<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\PhoneCast;
use App\Libraries\Enums\CustomerPhoneTypeEnum;
use App\Libraries\Values\PhoneValue;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property int $id
 * @property CustomerPhoneTypeEnum $type
 * @property PhoneValue $number
 * @property int $customer_id
 * @property Carbon $created_at
 */
#[Fillable(['type', 'customer_id', 'number', 'created_at'])]
final class CustomerPhone extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'customer_phone';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts()
    {
        return [
            'type' => CustomerPhoneTypeEnum::class,
            'number' => PhoneCast::class,
        ];
    }
}
