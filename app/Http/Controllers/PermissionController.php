<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::with('roles')->paginate(10);

        return view('permissions.index', compact('permissions'));
    }

    public function create(): View
    {
        return view('permissions.create');
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Permission::create(['name' => $validated['name']]);

        return redirect()->route('admin.permissions.index')
            ->with('status', 'Permission created successfully.');
    }

    public function edit(Permission $permission): View
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validated();

        $permission->update(['name' => $validated['name']]);

        return redirect()->route('admin.permissions.index')
            ->with('status', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('status', 'Permission deleted successfully.');
    }
}
