<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display all admin users.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $users = Admin::query()
            ->with('roles')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Show create user form.
     */
    public function create(): View
    {
        $roles = Role::query()
            ->where('guard_name', 'admin')
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a new admin user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:admins,email',
            ],
            'mobile' => [
                'required',
                'string',
                'max:30',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', 'admin'),
            ],
        ]);

        $role = Role::query()
            ->whereKey($validated['role_id'])
            ->where('guard_name', 'admin')
            ->firstOrFail();

        $user = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'password' => $validated['password'],
            'is_active' => $validated['is_active'],
            'role_id' => $role->id,
        ]);

        $user->syncRoles($role);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin user created successfully.');
    }

    /**
     * Show edit user form.
     */
    public function edit(Admin $user): View
    {
        $roles = Role::query()
            ->where('guard_name', 'admin')
            ->orderBy('name')
            ->get();

        $user->load('roles');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update an existing admin user.
     */
    public function update(Request $request, Admin $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($user->id),
            ],
            'mobile' => [
                'required',
                'string',
                'max:30',
            ],
            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', 'admin'),
            ],
        ]);

        // Prevent an admin from deactivating their own account.
        $currentAdminId = Auth::guard('admin')->id();
        if (
            (int) $user->id === (int) $currentAdminId
            && ! $validated['is_active']
        ) {
            return back()
                ->withInput()
                ->with('error', 'You cannot deactivate your own account.');
        }

        $role = Role::query()
            ->whereKey($validated['role_id'])
            ->where('guard_name', 'admin')
            ->firstOrFail();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->mobile = $validated['mobile'];
        $user->role_id = $role->id;
        $user->is_active = $validated['is_active'];

        // Update password only when a new password is provided.
        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();
        $user->syncRoles($role);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin user updated successfully.');
    }

    /**
     * Delete an admin user.
     */
    public function destroy(Admin $user): RedirectResponse
    {
        $currentAdminId = Auth::guard('admin')->id();

        // Prevent deleting the currently logged-in admin.
        if ((int) $user->id === (int) $currentAdminId) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the last Super Admin.
        if ($user->hasRole('Super Admin')) {
            $superAdminCount = Admin::query()
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Super Admin')
                        ->where('guard_name', 'admin');
                })
                ->count();
            if ($superAdminCount <= 1) {
                return redirect()
                    ->route('admin.users.index')
                    ->with(
                        'error',
                        'The last Super Admin account cannot be deleted.'
                    );
            }
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin user deleted successfully.');
    }
}
