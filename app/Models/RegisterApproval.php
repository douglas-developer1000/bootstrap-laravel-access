<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\Traits\FormatDatetimeProperty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

#[Fillable(['email', 'phone', 'token', 'expiration_data'])]
class RegisterApproval extends Model
{
    use HasFactory, FormatDatetimeProperty, Notifiable;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'expiration_data' => 'datetime',
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

    /**
     * Format the expiration_data to view
     *
     * @return string
     */
    public function getExpirationDataFormattedAttribute()
    {
        return $this->getPropertyFormatted('expiration_data');
    }
}
