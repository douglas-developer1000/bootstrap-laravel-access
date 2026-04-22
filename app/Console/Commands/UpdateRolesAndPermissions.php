<?php

namespace App\Console\Commands;

use App\Libraries\Enums\PermissionNameEnum;
use App\Libraries\Enums\RoleNameEnum;
use App\Libraries\Traits\EagerPermissionTrait;
use App\Libraries\Traits\EagerRoleTrait;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('permissions:update {--d}')]
#[Description('Insert the roles and permissions used by application into database (if not exists)')]
class UpdateRolesAndPermissions extends Command
{
    use EagerRoleTrait, EagerPermissionTrait;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roleNames = array_column(RoleNameEnum::cases(), 'value');
        $permissionNames = array_column(PermissionNameEnum::cases(), 'value');

        if (!$this->option('d')) {
            $this->makeRoles(...$roleNames);
            $this->info('Roles updated!');
            $this->makePermissions(...$permissionNames);
            $this->info('Permissions updated!');
        } else {
            $this->clearRoles(...$roleNames);
            $this->info('Roles updated!');
            $this->clearPermissions(...$permissionNames);
            $this->info('Permissions updated!');
        }
    }
}
