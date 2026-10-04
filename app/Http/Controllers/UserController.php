<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()->with('role')->latest()->paginate(20);
        $roles = Role::all();

        return view('users.index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:6'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::query()->create($validated);
        AuditService::log('create_user', 'Users', (string) $user->id, null, ['name' => $user->name, 'email' => $user->email]);

        return redirect()->route('users.index')->with('success', "Staff member '{$user->name}' added successfully.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:6'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        AuditService::log('update_user', 'Users', (string) $user->id, null, ['name' => $user->name]);

        return redirect()->route('users.index')->with('success', "Staff member '{$user->name}' updated.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $name = $user->name;
        $user->delete();

        AuditService::log('delete_user', 'Users', (string) $user->id, ['name' => $name], null);

        return redirect()->route('users.index')->with('success', "User {$name} deleted.");
    }
}
