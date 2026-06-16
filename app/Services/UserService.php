<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Dtos\UserCreationDto;
use App\Libraries\Traits\PicRequestHandleTrait;
use App\Libraries\Values\PhoneValue;
use App\Models\RegisterApproval;
use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use App\Services\Contracts\ImgStoragerInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Override;

final class UserService
{
    use PicRequestHandleTrait;

    public function __construct()
    {
        // ...
    }

    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class extends AbstractPaginatorIndex
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
                        fn(Builder $query) => $query->whereNotNull('deleted_at')
                    )
                    ->when(
                        !$trashed,
                        fn(Builder $query) => $query->whereNull('deleted_at')
                    );
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                return parent::attachQuery($request, $query)
                    ->when(
                        $this->paginator->buildSearch($request->only('name'), 'name'),
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

    protected function handleUserPhoto(Request $request, User $user): string|null
    {
        return (new ProfileService(
            app(ImgStoragerInterface::class, [
                'model' => $user,
                'key' => 'photo',
                'lastFolderName' => \strval($user->id)
            ])
        ))->storageProfileImg($request);
    }

    /**
     * Remove the register approval from database and return the user's phone
     * 
     * @param string $email The request's email
     * @param PhoneValue $phone The request's phone used as default phone value
     * @return PhoneValue The phone from the stored register approval or request
     */
    protected function deleteRegisterApproval(string $email, PhoneValue $phone): PhoneValue
    {
        /** @var RegisterApproval $registerApproval */
        $registerApproval = RegisterApproval::where(['email' => $email])->first(['id', 'phone']);
        $registerApproval->delete();
        if ($registerApproval->phone->getValue() !== NULL) {
            return $registerApproval->phone;
        }
        return $phone;
    }

    public function createInternalUser(Request $request): User|null
    {
        /**
         * @var string $name
         * @var string $email
         * @var string $password
         */
        [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ] = $request->only(['name', 'email', 'password']);
        return User::create(
            (new UserCreationDto(
                $name,
                $email,
                Hash::make($password)
            )
            )->putEmailVerifiedAt(Carbon::now())->toArray()
        );
    }

    public function createExternalUser(Request $request): void
    {
        $user = User::create((new UserCreationDto(
            $request->name,
            $request->email,
            $request->password
        ))->putPhone(
            phone: $this->deleteRegisterApproval($request->email, new PhoneValue($request->phone))
        )->toArray());
        $user->assignRole('user');

        event(new Registered($user));
    }

    public function updateUser(int $id, string $name)
    {
        User::where(['id' => $id])->update(['name' => $name]);
    }

    public function updateUserByOwner(Request $request, User $user)
    {
        $photoPath = $this->handleImg($request, 'photo', \strval($user->id), $user);

        $inputs = collect([
            ...$request->only(['name', 'password']),
            ...($photoPath ? ['photo' => $photoPath] : []),
        ])->filter(fn($val, $key) => $user->$key !== $val);

        $newPhone = new PhoneValue($request->validated('phone'));
        if (!$newPhone->equals($user->phone)) {
            $inputs->put('phone', $newPhone);
        }

        if ($inputs->isNotEmpty()) {
            $user->update($inputs->toArray());
        }
    }

    public function removeUser(int $id, bool $trashed = false)
    {
        if ($trashed) {
            User::onlyTrashed()->where(['id' => $id])->forceDelete();
        } else {
            User::where(['id' => $id])->delete();
        }
    }

    public function removeUserList(Request $request, Collection $qs)
    {
        $query = User::whereIn('id', $request->validated('remotion'));
        $forceDelete = $qs->contains(
            fn($value, $key) => $key === 'trashed' && $value === '1'
        );
        if ($forceDelete) {
            $query->forceDelete();
        } else {
            $query->delete();
        }
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
