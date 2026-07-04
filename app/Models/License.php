<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\Contracts\Licensable;
use App\Services\Contracts\LicenseStatusStateInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property int $id
 * @property int $plan_id
 * @property int $licensable_id
 * @property string $licensable_type
 * @property-read Licensable $licensable
 * @property float $price_paid
 * @property Carbon $starts_at
 * @property Carbon $expires_at
 * @property LicenseStatusEnum $status
 * @property bool $is_recurring
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $cancelled_at
 * @property-read float $prorata
 * @property-read bool $inTime
 * @property-read bool $isActivatable
 * @property-read bool $isReactivatable
 * @property-read bool $isPreCancellable
 * @property-read bool $isPostCancellable
 * @property-read bool $isExpirable
 * @property-read bool $allowPlanChanging
 * @property-read bool $isAbandonable
 * @property-read Plan $plan
 * @property-read LicenseStatusStateInterface $statusState
 */
#[Fillable([
    'plan_id',
    'licensable_id',
    'licensable_type',
    'price_paid',
    'starts_at',
    'expires_at',
    'status',
    'is_recurring',
    'cancelled_at',
])]
final class License extends Model
{
    protected LicenseStatusStateInterface $statusState;

    protected $casts = [
        'status' => LicenseStatusEnum::class,
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_recurring' => 'bool',
    ];

    public function licensable(): MorphTo
    {
        return $this->morphTo();
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function additionals(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'license_role');
    }

    protected function usedDays(): int
    {
        if (now()->greaterThanOrEqualTo($this->expires_at)) {
            return $this->usableDays();
        }
        if ($this->starts_at->greaterThanOrEqualTo(now())) {
            return 0;
        }
        return \intval($this->starts_at->diffInDays(now()));
    }

    protected function remainDays(): int
    {
        if (now()->greaterThanOrEqualTo($this->expires_at)) {
            return 0;
        }
        if ($this->starts_at->greaterThanOrEqualTo(now())) {
            return $this->usableDays();
        }
        return \intval(now()->diffInDays($this->expires_at));
    }

    protected function usableDays(): int
    {
        return \intval($this->starts_at->diffInDays($this->expires_at));
    }

    protected function priceByDay(): float
    {
        $days = $this->usableDays() ?: 1;

        return (($this->price_paid * 1000) / $days) / 1000;
    }

    protected function prorata(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => ($this->remainDays() * (1000 * $this->priceByDay())) / 1000
        )->withoutObjectCaching();
    }

    protected function inTime(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => now()->greaterThanOrEqualTo($this->starts_at) && now()->lessThanOrEqualTo($this->expires_at)
        )->withoutObjectCaching();
    }

    protected function isActivatable(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => $this->status === LicenseStatusEnum::PENDING
        )->withoutObjectCaching();
    }

    protected function isReactivatable(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => (
                $this->status === LicenseStatusEnum::ACTIVE &&
                $this->cancelled_at &&
                $this->inTime
            )
        )->withoutObjectCaching();
    }

    protected function isPreCancellable(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => (
                $this->status === LicenseStatusEnum::ACTIVE &&
                ! $this->cancelled_at &&
                $this->inTime
            )
        )->withoutObjectCaching();
    }

    protected function isPostCancellable(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => (
                $this->status === LicenseStatusEnum::ACTIVE &&
                $this->cancelled_at &&
                !$this->inTime
            )
        )->withoutObjectCaching();
    }

    protected function isExpirable(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => (
                $this->status === LicenseStatusEnum::ACTIVE &&
                !$this->cancelled_at &&
                !$this->inTime
            )
        )->withoutObjectCaching();
    }

    protected function allowPlanChanging(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => (
                $this->status === LicenseStatusEnum::ACTIVE &&
                $this->inTime
            )
        )->withoutObjectCaching();
    }

    protected function isAbandonable(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => $this->status === LicenseStatusEnum::PENDING
        )->withoutObjectCaching();
    }

    public function setStatusState(LicenseStatusStateInterface $statusState): self
    {
        $this->statusState = $statusState;
        return $this;
    }

    public function changePlan(): void
    {
        $this->statusState->changePlan();
    }

    public function activateLicense(): void
    {
        $this->statusState->activateLicense();
    }

    public function expireLicense(): void
    {
        $this->statusState->expireLicense();
    }

    public function cancelLicense(): void
    {
        $this->statusState->cancelLicense();
    }

    public function abandonLicense(): void
    {
        $this->statusState->abandonLicense();
    }

    protected static function booted(): void
    {
        self::retrieved(function (License $license): void {
            if ($license->status === LicenseStatusEnum::ACTIVE && $license->cancelled_at) {
                $license->setStatusState(
                    LicenseStatusEnum::CANCELED->parseStatusState($license)
                );
            } else {
                $license->setStatusState(
                    $license->status->parseStatusState($license)
                );
            }
        });
    }
}
