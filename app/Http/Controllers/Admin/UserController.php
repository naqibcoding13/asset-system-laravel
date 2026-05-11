<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()->latest()->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', [
            'user' => new User(),
            'units' => config('asset_system.units'),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'unit' => ['nullable', 'string', Rule::in(config('asset_system.units'))],
            'role' => ['required', 'in:admin,staff'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?: null,
            'unit' => $validated['unit'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berjaya ditambah.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', [
            'user' => $user,
            'units' => config('asset_system.units'),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'unit' => ['nullable', 'string', Rule::in(config('asset_system.units'))],
            'role' => ['required', 'in:admin,staff'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?: null,
            'unit' => $validated['unit'] ?? null,
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berjaya dikemaskini.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['Anda tidak boleh memadam akaun sendiri.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berjaya dipadam.');
    }
}
