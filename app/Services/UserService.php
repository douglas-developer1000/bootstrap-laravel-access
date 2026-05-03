<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\Dtos\UserCreationDto;
use App\Libraries\Utils\PhoneFormatter;
use App\Models\RegisterApproval;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

final class UserService
{
    public function __construct(
        protected UserRepository $repository,
    ) {
        // ...
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

    /**
     * Remove the register approval from database and return the user's phone
     * 
     * @param string $email The request's email
     * @param string $phone The request's phone used as default phone value
     * @return ?string The phone from the stored register approval or request
     */
    protected function deleteRegisterApproval(string $email, ?string $phone): string|null
    {
        $registerApproval = RegisterApproval::where(['email' => $email])->first(['id', 'phone']);
        $registerApproval->delete();
        return PhoneFormatter::clear($registerApproval->phone ?? $phone);
    }

    public function createExternalUser(Request $request): void
    {
        $user = User::create((new UserCreationDto(
            $request->name,
            $request->email,
            $request->password
        ))->putPhone(
            phone: $this->deleteRegisterApproval($request->email, $request->phone)
        )->toArray());
        $user->assignRole('user');

        event(new Registered($user));
    }

    public function update(int $id, array $attributes = []): int
    {
        return $this->repository->update($id, $attributes);
    }

    public function removeList(array $ids, bool $forceDelete = false)
    {
        return $this->repository->destroy($ids, $forceDelete);
    }

    public function restoreList(array $ids)
    {
        $this->repository->restore($ids);
    }
}
