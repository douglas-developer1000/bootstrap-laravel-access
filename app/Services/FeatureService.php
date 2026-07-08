<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Enums\BillingPeriodEnum;
use App\Libraries\Enums\PermissionNameEnum;
use App\Libraries\Enums\PlanNameEnum;
use App\Libraries\Enums\RoleNameEnum;
use App\Models\Plan;
use App\Models\Role;
use App\Models\RoleDescription;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Override;
use Spatie\Permission\Models\Permission;

final class FeatureService
{
    protected Collection $features;

    protected Collection $plans;

    public function __construct()
    {
        $this->features = collect([
            RoleNameEnum::SUPER_ADMIN->value => [],

            RoleNameEnum::FOR_SETTINGS->value => [
                PermissionNameEnum::HEADER_SETTINGS->value,
            ],
            RoleNameEnum::FOR_CUSTOMER->value => [
                PermissionNameEnum::CUSTOMER_INDEX->value,
                PermissionNameEnum::CUSTOMER_CREATE->value,
                PermissionNameEnum::CUSTOMER_STORE->value,
                PermissionNameEnum::CUSTOMER_SHOW->value,
                PermissionNameEnum::CUSTOMER_EDIT->value,
                PermissionNameEnum::CUSTOMER_UPDATE->value,
                PermissionNameEnum::CUSTOMER_DESTROY->value,
                PermissionNameEnum::CUSTOMER_RESTORE->value,
            ],
            RoleNameEnum::FOR_PRODUCT->value => [
                PermissionNameEnum::PRODUCT_INDEX->value,
                PermissionNameEnum::PRODUCT_SHOW->value,
                PermissionNameEnum::PRODUCT_CREATE->value,
                PermissionNameEnum::PRODUCT_EDIT->value,
                PermissionNameEnum::PRODUCT_STORE->value,
                PermissionNameEnum::PRODUCT_UPDATE->value,
                PermissionNameEnum::PRODUCT_DESTROY->value,
                PermissionNameEnum::PRODUCT_RESTORE->value,
            ],
            RoleNameEnum::FOR_STOCK_ENTRY->value => [
                PermissionNameEnum::STOCK_ENTRY_CREATE->value,
                PermissionNameEnum::STOCK_ENTRY_SPEND->value,
                PermissionNameEnum::STOCK_ENTRY_STORE->value,
            ],
            RoleNameEnum::FOR_RAW_EXIT->value => [
                PermissionNameEnum::RAW_EXIT_INDEX->value,
                PermissionNameEnum::RAW_EXIT_SHOW->value,
                PermissionNameEnum::RAW_EXIT_CREATE->value,
                PermissionNameEnum::RAW_EXIT_STORE->value,
                PermissionNameEnum::RAW_EXIT_DESTROY->value,
            ],
            RoleNameEnum::FOR_SALE_EXIT->value => [
                PermissionNameEnum::SALE_EXIT_INDEX->value,
                PermissionNameEnum::SALE_EXIT_SHOW->value,
                PermissionNameEnum::SALE_EXIT_CREATE->value,
                PermissionNameEnum::SALE_EXIT_STORE->value,
                PermissionNameEnum::SALE_EXIT_DESTROY->value,
            ],
            RoleNameEnum::FOR_SUPPLIER->value => [
                PermissionNameEnum::SUPPLIER_INDEX->value,
                PermissionNameEnum::SUPPLIER_CREATE->value,
                PermissionNameEnum::SUPPLIER_STORE->value,
                PermissionNameEnum::SUPPLIER_DESTROY->value,
                PermissionNameEnum::SUPPLIER_EDIT->value,
                PermissionNameEnum::SUPPLIER_SHOW->value,
                PermissionNameEnum::SUPPLIER_UPDATE->value,
                PermissionNameEnum::SUPPLIER_RESTORE->value,
            ],
            RoleNameEnum::FOR_DISCOUNT->value => [
                PermissionNameEnum::DISCOUNT_INDEX->value,
                PermissionNameEnum::DISCOUNT_CREATE->value,
                PermissionNameEnum::DISCOUNT_SHOW->value,
                PermissionNameEnum::DISCOUNT_EDIT->value,
                PermissionNameEnum::DISCOUNT_STORE->value,
                PermissionNameEnum::DISCOUNT_UPDATE->value,
                PermissionNameEnum::DISCOUNT_DESTROY->value,
                PermissionNameEnum::DISCOUNT_RESTORE->value,
            ],
            RoleNameEnum::FOR_EXCHANGE->value => [
                PermissionNameEnum::EXCHANGE_EXIT_INDEX->value,
                PermissionNameEnum::EXCHANGE_EXIT_CREATE->value,
                PermissionNameEnum::EXCHANGE_EXIT_DESTROY->value,
                PermissionNameEnum::EXCHANGE_EXIT_STORE->value,
            ],
            RoleNameEnum::FOR_PAYMENT_CARD->value => [
                PermissionNameEnum::PAYMENT_CARD_INDEX->value,
                PermissionNameEnum::PAYMENT_CARD_SHOW->value,
                PermissionNameEnum::PAYMENT_CARD_CREATE->value,
                PermissionNameEnum::PAYMENT_CARD_EDIT->value,
                PermissionNameEnum::PAYMENT_CARD_UPDATE->value,
                PermissionNameEnum::PAYMENT_CARD_STORE->value,
                PermissionNameEnum::PAYMENT_CARD_DESTROY->value,
                PermissionNameEnum::PAYMENT_CARD_RESTORE->value,
            ],
            RoleNameEnum::FOR_LOSS_EXIT->value => [
                PermissionNameEnum::GARBAGE_INDEX->value,
                PermissionNameEnum::LOSS_EXIT_SHOW->value,
                PermissionNameEnum::LOSS_EXIT_CREATE->value,
                PermissionNameEnum::LOSS_EXIT_STORE->value,
                PermissionNameEnum::LOSS_EXIT_DESTROY->value,
            ],
            RoleNameEnum::FOR_PERSONAL_USE_EXIT->value => [
                PermissionNameEnum::GARBAGE_INDEX->value,
                PermissionNameEnum::PERSONAL_USE_EXIT_SHOW->value,
                PermissionNameEnum::PERSONAL_USE_EXIT_CREATE->value,
                PermissionNameEnum::PERSONAL_USE_EXIT_STORE->value,
                PermissionNameEnum::PERSONAL_USE_EXIT_DESTROY->value,
            ],
            RoleNameEnum::FOR_DEMONSTRATION_EXIT->value => [
                PermissionNameEnum::GARBAGE_INDEX->value,
                PermissionNameEnum::DEMONSTRATION_EXIT_SHOW->value,
                PermissionNameEnum::DEMONSTRATION_EXIT_CREATE->value,
                PermissionNameEnum::DEMONSTRATION_EXIT_STORE->value,
                PermissionNameEnum::DEMONSTRATION_EXIT_DESTROY->value,
            ],
        ]);
        $this->plans = collect([
            PlanNameEnum::MODULE_A->value => collect()
                ->push([
                    'description' => PlanNameEnum::MODULE_A->description(),
                    'price' => 5.0,
                    'billing_period' => BillingPeriodEnum::MONTHLY,
                    'roles' => [
                        RoleNameEnum::FOR_SETTINGS->value,
                        RoleNameEnum::FOR_CUSTOMER->value,
                    ],
                ])
                ->all(),
            PlanNameEnum::MODULE_B->value => collect()
                ->push(
                    [
                        'description' => PlanNameEnum::MODULE_B->description(),
                        'price' => 7.5,
                        'billing_period' => BillingPeriodEnum::MONTHLY,
                        'roles' => [
                            RoleNameEnum::FOR_SETTINGS->value,
                            RoleNameEnum::FOR_PRODUCT->value,
                            RoleNameEnum::FOR_STOCK_ENTRY->value,
                            RoleNameEnum::FOR_RAW_EXIT->value,
                        ],
                        'additionals' => [
                            RoleNameEnum::FOR_SUPPLIER->value,
                            RoleNameEnum::FOR_DISCOUNT->value,
                        ],
                    ],
                )
                ->all(),
            PlanNameEnum::MODULE_C->value => collect()
                ->push([
                    'description' => PlanNameEnum::MODULE_C->description(),
                    'price' => 9.0,
                    'billing_period' => BillingPeriodEnum::MONTHLY,
                    'roles' => [
                        RoleNameEnum::FOR_SETTINGS->value,
                        RoleNameEnum::FOR_PRODUCT->value,
                        RoleNameEnum::FOR_STOCK_ENTRY->value,
                        RoleNameEnum::FOR_SALE_EXIT->value,
                    ],
                    'additionals' => [
                        RoleNameEnum::FOR_SUPPLIER->value,
                        RoleNameEnum::FOR_DISCOUNT->value,
                        RoleNameEnum::FOR_CUSTOMER->value,
                    ],
                ])
                ->all(),
            PlanNameEnum::MODULE_D->value => collect()
                ->push([
                    'description' => PlanNameEnum::MODULE_D->description(),
                    'price' => 10.0,
                    'billing_period' => BillingPeriodEnum::MONTHLY,
                    'roles' => [
                        RoleNameEnum::FOR_SETTINGS->value,
                        RoleNameEnum::FOR_PRODUCT->value,
                        RoleNameEnum::FOR_STOCK_ENTRY->value,
                        RoleNameEnum::FOR_SALE_EXIT->value,
                    ],
                    'additionals' => [
                        RoleNameEnum::FOR_SUPPLIER->value,
                        RoleNameEnum::FOR_DISCOUNT->value,
                        RoleNameEnum::FOR_CUSTOMER->value,
                        RoleNameEnum::FOR_PAYMENT_CARD->value,
                        RoleNameEnum::FOR_EXCHANGE->value,
                        RoleNameEnum::FOR_LOSS_EXIT->value,
                        RoleNameEnum::FOR_PERSONAL_USE_EXIT->value,
                        RoleNameEnum::FOR_DEMONSTRATION_EXIT->value,
                    ],
                ])
                ->all(),
        ]);
    }

    public function prepareIdlePermissionIndex(Request $request)
    {
        return (new class($this->features) extends AbstractPaginatorIndex
        {
            public function __construct(protected Collection $features)
            {
                parent::__construct();
            }

            #[Override]
            public function query(Request $request): QueryBuilder
            {
                return $this->findIdlePermissions();
            }

            protected function findIdlePermissions()
            {
                $roles = $this->features->keys();

                return Permission::whereDoesntHave('roles', fn(EloquentBuilder $query) => (
                    $query->whereIn('id', Role::whereIn('name', $roles->all())->get('id')->pluck('id')->all())
                ))->select()->getQuery();
            }

            #[Override]
            public function getSortColumns(): array
            {
                return [
                    'created_at',
                    'id',
                    'name',
                ];
            }
        })->prepareIndex(
            $request
        );
    }

    protected function syncRoleDescriptions(Role $role): void
    {
        $roleEnum = RoleNameEnum::from($role->name);
        $enumDescs = collect($roleEnum->descriptions());

        /** @var Collection $dbDescriptions */
        $dbDescriptions = $role->roleDescriptions()->get(['id', 'description']);

        // remove the deprecated from database
        RoleDescription::whereIn(
            'id',
            $dbDescriptions->filter(
                fn(RoleDescription $roleDesc) => !$enumDescs->contains(
                    fn(string $enumDesc) => $enumDesc === $roleDesc->description
                )
            )->pluck('id')
        )->delete();

        // insert the new descriptions from Enum
        $role->roleDescriptions()->createMany(
            $enumDescs->filter(
                fn(string $enumDesc) => !$dbDescriptions->contains(
                    fn(RoleDescription $roleDesc) => $roleDesc->description === $enumDesc
                )
            )->map(fn(string $desc) => ['description' => $desc])->all()
        );
    }

    public function update()
    {
        $this->features->each(function (array $permissions, string $roleName) {
            /** @var Role $role */
            $role = Role::firstOrCreate([
                'name' => $roleName,
            ], [
                'summary' => RoleNameEnum::from($roleName)->summary()
            ]);
            $this->syncRoleDescriptions($role);

            $role->givePermissionTo(
                ...collect($permissions)->map(
                    fn(string $permissionName) => Permission::firstOrCreate(
                        [
                            'name' => $permissionName,
                        ]
                    )
                )->filter(
                    fn(Permission $permission) => ! $role->hasPermissionTo($permission)
                )->all()
            );
        });
        $this->plans->each(function (array $planData, string $planName) {
            collect($planData)->each(function (array $data) use ($planName) {
                $planInfo = collect($data);

                /** @var BillingPeriodEnum $billingPeriod */
                $billingPeriod = $planInfo->get('billing_period');
                $slug = Str::of($planName)->append(' ')->append($billingPeriod->value)->when(
                    $planInfo->get('sub-label'),
                    fn(Stringable $value, string $subLabel) => $value->append($subLabel)
                )->slug()->toString();
                $name = Str::of(PlanNameEnum::from($planName)->toString())->when(
                    $planInfo->get('sub-label'),
                    fn(Stringable $value, string $subLabel) => $value->append($subLabel)
                )->toString();

                $plan = Plan::firstOrCreate([
                    'slug' => $slug,
                ], [
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $planInfo->get('description'),
                    'price' => $planInfo->get('price'),
                    'billing_period' => $billingPeriod,
                ]);

                $plan->roles()->sync(
                    Role::whereIn(
                        'name',
                        $planInfo->get('roles')
                    )->pluck('id')->all()
                );
                $additionals = collect($planInfo->get('additionals', []));
                $plan->roles()->syncWithoutDetaching(
                    Role::whereIn(
                        'name',
                        $additionals->all()
                    )->pluck('id')->mapWithKeys(fn(int $id) => [
                        $id => ['additional' => 1],
                    ])->all()
                );
            });
        });
    }
}
