<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RegisterDto;
use App\DTO\UpdateProfileDto;
use App\DTO\AdminUserDto;
use App\Jobs\SendRegistrationVerificationJob;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function register(RegisterDto $dto): User
    {
        $user = new User();
        $user->first_name = $dto->firstName;
        $user->last_name = $dto->lastName;
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->save();

        // По умолчанию каждому пользователю назначаем одну роль: user (если роль существует).
        $defaultRoleId = Role::query()->where('slug', Role::ROLE_USER)->value('id');
        if ($defaultRoleId) {
            $user->roles()->sync([(int) $defaultRoleId]);
        }

        SendRegistrationVerificationJob::dispatch($user->id);

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
    ): void {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'Invalid current password']);
        }

        $user->password = Hash::make($newPassword);
        $user->save();
    }

    public function createFromAdmin(AdminUserDto $dto): User
    {
        $user = new User();
        $user->first_name = $dto->firstName;
        $user->last_name = $dto->lastName;
        $user->email = $dto->email;
        $user->phone = $dto->phone;
        $user->status = $dto->status;
        $user->password = Hash::make((string) $dto->password);

        if ($dto->avatar) {
            $user->avatar = $this->storeAvatar($dto->avatar);
        }

        $user->save();
        $user->roles()->sync([$dto->roleId]);

        return $user;
    }

    public function updateFromAdmin(User $user, AdminUserDto $dto): User
    {
        $user->first_name = $dto->firstName;
        $user->last_name = $dto->lastName;
        $user->email = $dto->email;
        $user->phone = $dto->phone;
        $user->status = $dto->status;

        if ($dto->avatar) {
            $this->deleteAvatarIfExists($user->avatar);
            $user->avatar = $this->storeAvatar($dto->avatar);
        }

        $user->save();
        $user->roles()->sync([$dto->roleId]);

        return $user;
    }

    public function resetPassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->save();
    }

    public function delete(User $user): void
    {
        $this->deleteAvatarIfExists($user->avatar);
        $user->delete();
    }

    private function storeAvatar(\Illuminate\Http\UploadedFile $file): string
    {
        return $file->store('avatars', 'public');
    }

    private function deleteAvatarIfExists(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
