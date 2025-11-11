<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\RegisterDto;
use App\DTO\UpdateProfileDto;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
    )
    {}
    public function register(RegisterDto $dto): User
    {
        $user = new User();
        $user->first_name = $dto->firstName;
        $user->last_name = $dto->lastName;
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->save();

        // TODO: после изучения очередей добавить событие для отправки приветственного письма:
        // event(new Registered($user));

        return $user;
    }

    /**
     * @throws AuthenticationException
     */
    public function updateProfile(UpdateProfileDto $dto): void
    {
        $user = Auth::user();

        if ($user === null) {
            throw new AuthenticationException('Пользователь не авторизован');
        }

        $user->fill($dto->toArray());
        $user->save();
    }

    /**
     * @throws ValidationException
     */
    public function updatePassword(
        User $user,
        string $currentPassword,
        string $newPassword
    ): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'Invalid current password']);
        }

        $user->password = Hash::make($newPassword);
        $user->save();
    }
}
