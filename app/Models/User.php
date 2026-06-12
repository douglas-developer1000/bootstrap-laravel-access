<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\PhoneCast;
use App\Notifications\PreResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property null|\App\Libraries\Values\PhoneValue $phone
 * @property null|\Illuminate\Support\Carbon $email_verified_at
 * @property null|\Illuminate\Support\Carbon $deleted_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 */
#[Fillable(['name', 'email', 'password', 'phone', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
final class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

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

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PreResetPasswordNotification($token));
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function isModelMine(Model $model): bool
    {
        return $this->id === $model->user_id;
    }
}
