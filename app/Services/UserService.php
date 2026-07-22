<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\PlanAssigned;
use App\Facades\Paginator;
use App\Facades\TimingProtection;
use App\Libraries\Traits\PicRequestHandleTrait;
use App\Libraries\Values\PhoneValue;
use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use App\Services\Contracts\ImgStoragerInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Override;

final class UserService
{
    use PicRequestHandleTrait;

    public function __construct(
        protected PlanService $planSvc,
        protected LicenseService $licenseSvc,
    ) {
        // ...
    }

    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class() extends AbstractPaginatorIndex
        {
            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'id', 'name'];
            }

            #[Override]
            public function query(Request $request): Builder
            {
                $trashed = $request->boolean('trashed');

                return User::getQuery()
                    ->when(
                        $trashed,
                        fn (Builder $query) => $query->whereNotNull('deleted_at')
                    )
                    ->when(
                        ! $trashed,
                        fn (Builder $query) => $query->whereNull('deleted_at')
                    );
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                return parent::attachQuery($request, $query)
                    ->when(
                        Paginator::buildSearch($request->only('name'), 'name'),
                        function (Builder $query, string $nameSearch) {
                            $nameSearch = addcslashes($nameSearch, '%_');

                            return $query->whereLike('name', "%{$nameSearch}%");
                        }
                    );
            }
        })->prepareIndex(
            $request,
            'id',
            'name',
            'email',
            'created_at'
        );
    }

    protected function handleUserPhoto(Request $request, User $user): ?string
    {
        return (new ProfileService(
            app(ImgStoragerInterface::class, [
                'model' => $user,
                'key' => 'photo',
                'lastFolderName' => \strval($user->id),
            ])
        ))->storageProfileImg($request);
    }

    public function createInternalUser(Request $request): bool
    {
        try {
            DB::transaction(function () use ($request) {
                $plan = $this->planSvc->parsePlan($request->input('plan'));

                $user = User::create([
                    ...$request->only(['name', 'email']),
                    'password' => Hash::make($request->input('password')),
                    'email_verified_at' => now(),
                ]);

                $license = $this->licenseSvc->bindPlan(
                    $plan,
                    $user,
                    $request->boolean('recurring'),
                    $request->input('additionals', []),
                );

                PlanAssigned::dispatch($user, $plan, $license);
            });

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function createExternalUser(Request $request): bool
    {
        return TimingProtection::execWithProtection(function () use ($request) {
            $user = User::firstWhere('email', $request->input('email'));

            if ($user) {
                if ($user->hasVerifiedEmail()) {
                    TimingProtection::fakePasswordVerification($request->input('password'));

                    return false;
                }

                TimingProtection::gaussianDelay(400, 150);
                $user->sendEmailVerificationNotification();

                return false;
            }

            $user = User::create([
                ...$request->only(['name', 'email', 'password']),
                'phone' => new PhoneValue($request->input('phone')),
            ]);

            TimingProtection::gaussianDelay(400, 150);

            event(new Registered($user));

            return true;
        });
    }

    public function updateUser(User $user, string $name): void
    {
        $user->update(['name' => $name]);
    }

    public function updateUserByOwner(Request $request, User $user)
    {
        $photoPath = $this->handleImg($request, 'photo', \strval($user->id), $user);

        $inputs = collect([
            ...$request->only(['name', 'password']),
            ...($photoPath ? ['photo' => $photoPath] : []),
        ])->filter(fn ($val, $key) => $val !== $user->$key);

        $newPhone = new PhoneValue($request->validated('phone'));
        if (! $newPhone->equals($user->phone)) {
            $inputs->put('phone', $newPhone);
        }

        if ($inputs->isNotEmpty()) {
            $user->update($inputs->toArray());
        }
    }

    public function removeUser(User $user, bool $trashed = false)
    {
        if ($trashed) {
            $user->forceDelete();
        } else {
            $user->delete();
        }
    }

    public function removeUserList(Request $request, Collection $qs)
    {
        $forceDelete = $qs->contains(
            fn ($value, $key) => $key === 'trashed' && $value === '1'
        );

        when(
            $forceDelete,
            fn () => User::onlyTrashed(),
            fn () => User::query()
        )->findMany($request->validated('remotion'))->each(
            fn (User $user) => $this->removeUser($user, $forceDelete)
        );
    }

    public function restore(int $id): void
    {
        User::onlyTrashed()->where(['id' => $id])->restore();
    }

    public function restoreGroup(Request $request)
    {
        User::onlyTrashed()->whereIn('id', $request->validated('restoration'))->restore();
    }
}
