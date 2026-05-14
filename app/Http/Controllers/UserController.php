<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['email_verified_at'] = isset($validated['email_verified'])
            ? now()
            : null;

        unset($validated['email_verified']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
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

        $user->update($validated);

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
