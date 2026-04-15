<?php

namespace App\Models;

use App\Libraries\Utils\PhoneFormatter;
use App\Models\Traits\FormatDatetimeProperty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['email', 'phone'])]
class RegisterOrder extends Model
{
    /** @use HasFactory<RegisterOrder> */
    use HasFactory, FormatDatetimeProperty;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Sanitize the phone number column value
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = PhoneFormatter::clear($value);
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
