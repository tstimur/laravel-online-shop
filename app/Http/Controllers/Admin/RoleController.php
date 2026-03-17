<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTO\RoleDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleStoreRequest;
use App\Http\Requests\Admin\RoleUpdateRequest;
use App\Models\Role;
use App\Service\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create');
    }

    public function store(RoleStoreRequest $request, RoleService $service): RedirectResponse
    {
        $service->create(RoleDto::fromRequest($request));

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(
        RoleUpdateRequest $request,
        Role $role,
        RoleService $service
    ): RedirectResponse {
        $service->update($role, RoleDto::fromRequest($request));

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated.');
    }

    public function destroy(Role $role, RoleService $service): RedirectResponse
    {
        try {
            $service->delete($role);
        } catch (ValidationException $exception) {
            $message = $exception->errors()['role'][0] ?? 'Unable to delete role.';

            return redirect()
                ->route('admin.roles.index')
                ->with('error', $message);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted.');
    }
}
