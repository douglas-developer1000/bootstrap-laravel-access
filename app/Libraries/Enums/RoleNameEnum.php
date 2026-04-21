<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

enum RoleNameEnum: string
{
    case SUPER_ADMIN = 'super-admin';
    case USER = 'user';
}
