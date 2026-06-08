<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\PhoneCast;
use App\Models\Traits\FormatDatetimeProperty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

/**
 * @property-read string $created_at_formatted
 * @property-read string $updated_at_formatted
 */
#[Fillable(['email', 'phone'])]
final class RegisterOrder extends Model
{
    /** @use HasFactory<RegisterOrder> */
    use HasFactory, FormatDatetimeProperty;

    protected function casts(): array
    {
        return [
            'phone' => PhoneCast::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Format the created_at to view
     *
     * @return string
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->getPropertyFormatted('created_at');
    }

    /**
     * Format the updated_at to view
     *
     * @return string
     */
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->getPropertyFormatted('updated_at');
    }
}
