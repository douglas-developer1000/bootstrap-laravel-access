<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\CustomerPhoneTypeEnum;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['type', 'customer_id', 'number', 'created_at'])]
class CustomerPhone extends Model
{
    public const UPDATED_AT = NULL;

    protected $table = 'customer_phone';

    protected $casts = [
        'type' => CustomerPhoneTypeEnum::class
    ];
}
