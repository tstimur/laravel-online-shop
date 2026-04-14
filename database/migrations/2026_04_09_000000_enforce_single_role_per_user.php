<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Перед добавлением UNIQUE(user_id) приводим данные к виду "одна роль на пользователя".
        // Приоритет: admin > manager > user > любая другая.
        $roleIdsBySlug = DB::table('roles')
            ->whereIn('slug', ['admin', 'manager', 'user'])
            ->pluck('id', 'slug')
            ->map(static fn ($id) => (int) $id)
            ->all();

        $priorityRoleIds = array_values(array_filter([
            $roleIdsBySlug['admin'] ?? null,
            $roleIdsBySlug['manager'] ?? null,
            $roleIdsBySlug['user'] ?? null,
        ]));

        $userIdsWithManyRoles = DB::table('role_user')
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_id')
            ->map(static fn ($id) => (int) $id)
            ->all();

        foreach ($userIdsWithManyRoles as $userId) {
            $roleIds = DB::table('role_user')
                ->where('user_id', $userId)
                ->pluck('role_id')
                ->map(static fn ($id) => (int) $id)
                ->all();

            if ($roleIds === []) {
                continue;
            }

            $keepRoleId = null;
            foreach ($priorityRoleIds as $priorityRoleId) {
                if (in_array($priorityRoleId, $roleIds, true)) {
                    $keepRoleId = $priorityRoleId;
                    break;
                }
            }

            $keepRoleId ??= min($roleIds);

            DB::table('role_user')
                ->where('user_id', $userId)
                ->where('role_id', '<>', $keepRoleId)
                ->delete();
        }

        Schema::table('role_user', function (Blueprint $table) {
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_user', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });
    }
};

