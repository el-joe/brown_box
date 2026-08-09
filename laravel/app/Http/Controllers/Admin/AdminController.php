<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = Admin::query()->with('roles');

        if ($name = $request->string('name')->toString()) {
            $query->where('name', 'like', '%'.$name.'%');
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $admins = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.admins.index', [
            'admins' => $admins,
            'filters' => $request->only(['name', 'is_active']),
        ]);
    }

    public function create(): View
    {
        return view('admin.admins.form', [
            'admin' => new Admin(),
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function store(AdminRequest $request): RedirectResponse
    {
        $admin = Admin::query()->create($this->mapData($request));
        $admin->syncRoles($request->input('roles', []));

        return redirect()->route('admin.admins.index')->with('success', __('Admin created successfully.'));
    }

    public function edit(Admin $admin): View
    {
        return view('admin.admins.form', [
            'admin' => $admin,
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function update(AdminRequest $request, Admin $admin): RedirectResponse
    {
        $admin->update($this->mapData($request));
        $admin->syncRoles($request->input('roles', []));

        return redirect()->route('admin.admins.index')->with('success', __('Admin updated successfully.'));
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        abort_if($admin->id === auth('admin')->id(), 422);

        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', __('Admin deleted successfully.'));
    }

    public function toggleActive(Admin $admin): JsonResponse
    {
        abort_if($admin->id === auth('admin')->id(), 422);

        $admin->update(['is_active' => ! $admin->is_active]);

        return response()->json(['success' => true, 'is_active' => $admin->is_active]);
    }

    private function mapData(AdminRequest $request): array
    {
        $data = $request->safe()->only(['name', 'email']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->string('password'));
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('admins', 'public');
        }

        return $data;
    }
}
