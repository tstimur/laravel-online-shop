<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'User',
                'slug' => Role::ROLE_USER,
            ],
            [
                'name' => 'Admin',
                'slug' => Role::ROLE_ADMIN,
            ],
            [
                'name' => 'Manager',
                'slug' => Role::ROLE_MANAGER,
            ],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name']]
            );
        }
    }
}
