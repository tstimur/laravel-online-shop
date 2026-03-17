<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RoleDto;
use App\Models\Role;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public function create(RoleDto $dto): Role
    {
        return Role::query()->create($dto->toArray());
    }

    public function update(Role $role, RoleDto $dto): Role
    {
        $role->fill($dto->toArray());
        $role->save();

        return $role;
    }

    /**
     * @throws ValidationException
     */
    public function delete(Role $role): void
    {
        if (in_array($role->slug, [Role::ROLE_USER, Role::ROLE_ADMIN, Role::ROLE_MANAGER], true)) {
            throw ValidationException::withMessages([
                'role' => 'System roles cannot be deleted.',
            ]);
        }

        $role->delete();
    }
}
