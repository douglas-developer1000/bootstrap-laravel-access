<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\CustomerContactEnum;
use App\Libraries\Enums\DayPeriodsEnum;
use App\Libraries\Traits\HandlerAnonymousTrait;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Override;

/**
 * @property string $name
 * @property string $email
 * @property string $hostess
 * @property Carbon $birthdate
 * @property string $contact
 * @property string $schedule
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $native
 * @property int $user_id
 * @property-read Collection<DayPeriodsEnum> $schedule_list
 * @property-read Collection<CustomerContactEnum> $contact_list
 */
#[Fillable(['name', 'email', 'hostess', 'birthdate', 'contact', 'schedule', 'native', 'user_id'])]
final class Customer extends Model
{
    use HandlerAnonymousTrait, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts()
    {
        return [
            'native' => 'boolean',
        ];
    }

    /**
     * Parse the contact stored by database to a CustomerContactEnum list
     *
     * @return Collection<CustomerContactEnum>
     */
    public function getContactListAttribute(): Collection
    {
        return collect(array_filter(explode('+', $this->contact ?? '')))->map(
            fn (string $text) => CustomerContactEnum::from($text)
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
            fn (string $text) => DayPeriodsEnum::from($text)
        );
    }

    public function phones(): HasMany
    {
        return $this->hasMany(CustomerPhone::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
