<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\CustomerContactEnum;
use App\Libraries\Enums\DayPeriodsEnum;
use App\Models\Traits\FormatDatetimeProperty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Collection;

#[Fillable(['name', 'email', 'hostess', 'birthdate', 'contact', 'schedule', 'user_id'])]
final class Customer extends Model
{
    use FormatDatetimeProperty;

    /**
     * Parse the contact stored by database to a CustomerContactEnum list
     *
     * @return Collection<CustomerContactEnum>
     */
    public function getContactListAttribute(): Collection
    {
        return collect(array_filter(explode('+', $this->contact ?? '')))->map(
            fn(string $text) => CustomerContactEnum::from($text)
        );
    }

    /**
     * Parse the schedule stored by database to a DayPeriodsEnum list
     * 
     * @return Collection<DayPeriodsEnum>
     */
    public function getScheduleListAttribute(): Collection
    {
        return collect(array_filter(explode('+', $this->schedule ?? '')))->map(
            fn(string $text) => DayPeriodsEnum::from($text)
        );
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

    public function phones()
    {
        return $this->hasMany(CustomerPhone::class);
    }
}
