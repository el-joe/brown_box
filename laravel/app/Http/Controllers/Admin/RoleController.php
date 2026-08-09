<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->withCount(['permissions', 'users as admins_count'])
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.form', [
            'role' => new Role(),
            'groups' => $this->groupedPermissions(),
            'assigned' => [],
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $role = Role::query()->create(['name' => $request->validated('name'), 'guard_name' => 'admin']);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.roles.index')->with('success', __('Role created successfully.'));
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.form', [
            'role' => $role,
            'groups' => $this->groupedPermissions(),
            'assigned' => $role->permissions->pluck('id')->all(),
        ]);
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        abort_if($role->name === 'super_admin', 422, __('The super admin role cannot be modified.'));

        $role->update(['name' => $request->validated('name')]);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.roles.index')->with('success', __('Role updated successfully.'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->name === 'super_admin', 422, __('The super admin role cannot be deleted.'));
        abort_if($role->users()->exists(), 422, __('Cannot delete a role that is assigned to admins.'));

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', __('Role deleted successfully.'));
    }

    private function groupedPermissions(): array
    {
        $permissions = Permission::query()->orderBy('name')->get()->keyBy('name');

        $groups = [];

        foreach (\Database\Seeders\PermissionSeeder::GROUPS as $group => $names) {
            $groups[$group] = collect($names)
                ->map(fn (string $name) => $permissions->get($name))
                ->filter()
                ->values();
        }

        return $groups;
    }
}
