<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->where('guard_name', 'admin')
            ->with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::query()
            ->where('guard_name', 'admin')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permission) => explode('.', $permission->name)[0]);

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9 _-]+$/',
                Rule::unique('roles', 'name')->where(
                    fn ($query) => $query->where('guard_name', 'admin')
                ),
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where(
                    fn ($query) => $query->where('guard_name', 'admin')
                ),
            ],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'admin',
        ]);

        $permissionNames = $validated['permissions'] ?? [];
        $role->syncPermissions($permissionNames);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        abort_unless($role->guard_name === 'admin', 404);
        $permissions = Permission::query()
            ->where('guard_name', 'admin')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permission) => explode('.', $permission->name)[0]);

        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_unless($role->guard_name === 'admin', 404);
        // Protect the Super Admin role from modification.
        if ($role->name === 'Super Admin') {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Super Admin role cannot be edited.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9 _-]+$/',
                Rule::unique('roles', 'name')
                    ->where(fn ($query) => $query->where('guard_name', 'admin'))
                    ->ignore($role->id),
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where(
                    fn ($query) => $query->where('guard_name', 'admin')
                ),
            ],
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_unless($role->guard_name === 'admin', 404);
        if ($role->name === 'Super Admin') {
            return back()->with(
                'error',
                'The Super Admin role cannot be deleted.'
            );
        }

        $role->delete();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
