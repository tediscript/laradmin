<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $sortBy = request('sort_by', 'created_at');
        $sortDirection = request('sort_direction', 'desc');
        $search = request('search');

        $allowedSortColumns = ['name', 'email', 'email_verified_at', 'timezone', 'created_at'];

        if (! in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        if (! in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortBy, $sortDirection)
            ->paginate(10)
            ->appends(request()->query());

        return view('users.index', compact('users', 'sortBy', 'sortDirection', 'search'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['email_verified_at'] = isset($validated['email_verified'])
            ? now()
            : null;

        unset($validated['email_verified']);

        if (isset($validated['timezone']) && $validated['timezone'] === '') {
            $validated['timezone'] = null;
        }

        $roles = $validated['roles'] ?? [];
        unset($validated['roles']);

        $user = User::create($validated);
        $user->syncRoles($roles);

        return redirect()->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        if (isset($validated['email_verified'])) {
            $validated['email_verified_at'] = $user->email_verified_at ?? now();
        } else {
            $validated['email_verified_at'] = null;
        }

        unset($validated['email_verified']);

        if (isset($validated['timezone']) && $validated['timezone'] === '') {
            $validated['timezone'] = null;
        }

        $user->update($validated);

        if (array_key_exists('roles', $validated)) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('admin.users.index')
            ->with('status', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'User deleted successfully.');
    }
}
