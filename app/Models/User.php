<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\PhoneCast;
use App\Libraries\Values\PhoneValue;
use App\Models\Contracts\Billable;
use App\Models\Contracts\HasLicenseHandling;
use App\Models\Contracts\HasRoleHandling;
use App\Notifications\PreResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Override;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property null|PhoneValue $phone
 * @property null|Carbon $email_verified_at
 * @property null|Carbon $deleted_at
 * @property Carbon $updated_at
 * @property Carbon $created_at
 * @property-read Collection<ProductCategory> $productCategories
 */
#[Fillable(['name', 'email', 'password', 'phone', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
final class User extends Authenticatable implements Billable, HasLicenseHandling, HasRoleHandling, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'phone' => PhoneCast::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new PreResetPasswordNotification($token));
    }

    public function productCategories(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function isModelMine(Model $model): bool
    {
        return $this->id === $model->user_id;
    }

    public function licenses(): MorphMany
    {
        return $this->morphMany(License::class, 'licensable');
    }

    public function activeLicense(): MorphOne
    {
        return $this->morphOne(License::class, 'licensable')
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->latest();
    }

    #[Override]
    public function getBillingEmail(): string
    {
        return $this->email;
    }

    #[Override]
    protected static function booted()
    {
        self::deleting(function (User $user) {
            if ($user->isForceDeleting()) {
                $user->licenses()->delete();
            }
        });
    }
}
