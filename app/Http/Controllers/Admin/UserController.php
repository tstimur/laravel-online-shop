<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTO\AdminUserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserPasswordResetRequest;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Service\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('roles')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::query()->orderBy('name')->get();
        $defaultRoleId = Role::query()->where('slug', Role::ROLE_USER)->value('id');

        return view('admin.users.create', compact('roles', 'defaultRoleId'));
    }

    public function store(UserStoreRequest $request, UserService $service): RedirectResponse
    {
        $user = $service->createFromAdmin(AdminUserDto::fromRequest($request));

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User created.');
    }

    public function show(User $user): View
    {
        $user->load('roles');

        $orders = Order::query()
            ->where('user_id', $user->id)
            ->withCount('items')
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.users.show', compact('user', 'orders'));
    }

    public function edit(User $user): View
    {
        $roles = Role::query()->orderBy('name')->get();
        $user->load('roles');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserUpdateRequest $request, User $user, UserService $service): RedirectResponse
    {
        $service->updateFromAdmin($user, AdminUserDto::fromRequest($request));

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated.');
    }

    public function destroy(User $user, UserService $service): RedirectResponse
    {
        $service->delete($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted.');
    }

    public function resetPassword(
        UserPasswordResetRequest $request,
        User $user,
        UserService $service
    ): RedirectResponse {
        $service->resetPassword($user, $request->validated('password'));

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Password reset.');
    }
}
