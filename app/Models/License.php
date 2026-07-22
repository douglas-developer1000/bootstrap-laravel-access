<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\BigDecimalCast;
use App\Exceptions\InvalidStatusException;
use App\Libraries\Enums\GatewayTypeEnum;
use App\Libraries\Enums\InvoicePaymentTypeEnum;
use App\Libraries\Enums\InvoiceStatusEnum;
use App\Libraries\Enums\LicenseStatusEnum;
use App\Libraries\Enums\LocaleEnum;
use App\Models\Contracts\HasRoleHandling;
use App\Models\Contracts\Licensable;
use App\Services\Contracts\LicenseStatusStateInterface;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

/**
 * @property int $id
 * @property int $plan_id
 * @property int $licensable_id
 * @property string $licensable_type
 * @property-read Licensable $licensable
 * @property BigDecimal $price_paid
 * @property Carbon $starts_at
 * @property Carbon $expires_at
 * @property LicenseStatusEnum $status
 * @property bool $is_recurring
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $cancelled_at
 * @property int $coupon_id
 * @property-read BigDecimal $prorata
 * @property-read bool $inTime
 * @property-read bool $isActivatable
 * @property-read bool $isReactivatable
 * @property-read bool $isPreCancellable
 * @property-read bool $isPostCancellable
 * @property-read bool $isExpirable
 * @property-read bool $allowPlanChanging
 * @property-read bool $isAbandonable
 * @property-read Plan $plan
 * @property-read Collection<Role> $additionals
 * @property-read LicenseStatusStateInterface $statusState
 * @property-read BigDecimal $priceAdditionals
 * @property-read BigDecimal $paidInvoicesAmount
 * @property-read BigDecimal $pendingInvoicesAmount
 * @property-read Collection<Credit> $credits
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
    'coupon_id',
])]
final class License extends Model
{
    public const float PRICE_ADDITIONAL = 0.5;

    protected LicenseStatusStateInterface $statusState;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts()
    {
        return [
            'status' => LicenseStatusEnum::class,
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_recurring' => 'boolean',
            'price_paid' => BigDecimalCast::class,
        ];
    }

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

    protected function priceByDay(): BigDecimal
    {
        $days = $this->usableDays() ?: 1;

        return BigDecimal::of($this->price_paid)
            ->dividedBy($days, 3, RoundingMode::Floor);
    }

    protected function priceAdditionals(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value) {
                $additionalPrice = License::PRICE_ADDITIONAL;

                return BigDecimal::of("{$additionalPrice}")->multipliedBy(
                    $this->additionals()->count()
                );
            }
        )->withoutObjectCaching();
    }

    protected function prorata(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value) {
                $priceByDay = $this->priceByDay();

                return BigDecimal::of($this->remainDays())
                    ->multipliedBy(
                        BigDecimal::of($priceByDay)
                    )
                    ->plus($this->priceAdditionals);
            }
        )->withoutObjectCaching();
    }

    protected function inTime(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => now()->greaterThanOrEqualTo($this->starts_at) && now()->lessThanOrEqualTo($this->expires_at)
        )->withoutObjectCaching();
    }

    protected function isActivatable(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => $this->status === LicenseStatusEnum::PENDING
        )->withoutObjectCaching();
    }

    protected function isReactivatable(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => (
                $this->status === LicenseStatusEnum::ACTIVE &&
                $this->cancelled_at &&
                $this->inTime
            )
        )->withoutObjectCaching();
    }

    protected function isPreCancellable(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => (
                $this->status === LicenseStatusEnum::ACTIVE &&
                ! $this->cancelled_at &&
                $this->inTime
            )
        )->withoutObjectCaching();
    }

    protected function isPostCancellable(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => (
                $this->status === LicenseStatusEnum::ACTIVE &&
                $this->cancelled_at &&
                ! $this->inTime
            )
        )->withoutObjectCaching();
    }

    protected function isExpirable(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => (
                $this->status === LicenseStatusEnum::ACTIVE &&
                ! $this->cancelled_at &&
                ! $this->inTime
            )
        )->withoutObjectCaching();
    }

    protected function allowPlanChanging(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => (
                $this->status === LicenseStatusEnum::ACTIVE &&
                $this->inTime
            )
        )->withoutObjectCaching();
    }

    protected function isAbandonable(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => $this->status === LicenseStatusEnum::PENDING
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

    public function abandonLicense(InvoiceStatusEnum $invoiceStatus, string $reason = 'Abandono de checkout'): void
    {
        $this->statusState->abandonLicense($reason, $invoiceStatus);
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function paidInvoicesAmount(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value) {
                $amount = $this->invoices()->where('status', InvoiceStatusEnum::PAID)->sum('amount');

                return BigDecimal::of($amount);
            }
        )->withoutObjectCaching();
    }

    public function pendingInvoicesAmount(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value) {
                $amount = $this->invoices()->where('status', InvoiceStatusEnum::PENDING)->sum('amount');

                return BigDecimal::of($amount);
            }
        )->withoutObjectCaching();
    }

    /**
     * Pull the rule names list bound to license
     *
     * @return array<int, string>
     */
    public function pullBoundRoleNames(): array
    {
        return [
            ...$this->plan->roles()->wherePivot(
                'additional',
                0
            )->get()->pluck('name'),
            ...$this->additionals->pluck('name'),
        ];
    }

    public function releaseLicensable(): self
    {
        if ($this->status !== LicenseStatusEnum::ACTIVE) {
            throw InvalidStatusException::licenseRelease($this->id);
        }

        $licensable = $this->licensable;
        if ($licensable instanceof HasRoleHandling) {
            $licensable->assignRole(
                $this->pullBoundRoleNames()
            );
        }

        return $this;
    }

    public function annullCredits(string $reason): void
    {
        $this->credits()->createMany(
            $this->credits()->pluck('amount')->map(
                fn (BigDecimal $amount) => [
                    'licensable_type' => $this->licensable_type,
                    'licensable_id' => $this->licensable_id,
                    'amount' => $amount->negated(),
                    'description' => "Estorno de crédito automático: {$reason}",
                ]
            )
        );
    }

    /**
     * The InvoiceStatusEnum::EXPIRED state is only used during the job used to
     * abandon licenses (automatic change from system). Any other cases must use
     * the InvoiceStatusEnum::VOIDED state because they symbolize an manual change.
     */
    public function annulInvoices(InvoiceStatusEnum $status): void
    {
        if (
            ! collect([
                InvoiceStatusEnum::VOIDED,
                InvoiceStatusEnum::EXPIRED,
            ])->contains($status)
        ) {
            throw InvalidStatusException::invoiceAnnulment($this->id);
        }

        $this->invoices()
            ->where('invoices.status', InvoiceStatusEnum::PENDING)
            ->update([
                'status' => $status,
                'voided' => $status === InvoiceStatusEnum::VOIDED
                    ? now(LocaleEnum::BR->getTimezone())
                    : null,
            ]);

        $this->invoices()
            ->where('invoices.status', InvoiceStatusEnum::FAILED)
            ->update([
                'expired_at' => now(LocaleEnum::BR->getTimezone()),
            ]);
    }

    public function payInvoices(
        ?GatewayTypeEnum $gateway = null,
        ?InvoicePaymentTypeEnum $paymentMethod = null,
        ?array $paymentDetails = null,
    ): void {
        $this->invoices()
            ->where('invoices.status', InvoiceStatusEnum::PENDING)
            ->update([
                'status' => InvoiceStatusEnum::PAID,
                'gateway' => $gateway,
                'payment_method' => $paymentMethod,
                'payment_details' => $paymentDetails,
            ]);
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
