<?php

declare(strict_types=1);

namespace App\Libraries\Dtos;

use Carbon\Carbon;

final class UserCreationDto
{
    protected ?Carbon $emailVerifiedAt = NULL;
    protected ?string $phone = NULL;

    public function __construct(
        protected string $name,
        protected string $email,
        protected string $password
    ) {
        // ...
    }

    public function putEmailVerifiedAt(Carbon $emailVerifiedAt): UserCreationDto
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
        return $this;
    }

    public function putPhone(?string $phone): UserCreationDto
    {
        $this->phone = $phone;
        return $this;
    }

    public function toArray()
    {
        $attributes = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
        if ($this->emailVerifiedAt !== null) {
            $attributes['email_verified_at'] = $this->emailVerifiedAt;
        }
        if ($this->phone !== null) {
            $attributes['phone'] = $this->phone;
        }
        return $attributes;
    }
}
