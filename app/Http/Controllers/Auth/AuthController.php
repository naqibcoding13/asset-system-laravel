<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'username' => 'Username atau password tidak sah.',
            ])->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(
            Auth::user()->role === 'admin'
                ? route('admin.dashboard')
                : route('staff.requests.create')
        );
    }

    public function showRegister()
    {
        return view('auth.register', [
            'units' => config('asset_system.units'),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')],
            'unit' => ['required', 'string', Rule::in(config('asset_system.units'))],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?: null,
            'unit' => $validated['unit'],
            'role' => 'staff',
            'password' => Hash::make($validated['password']),
        ]);

        foreach (User::query()->where('role', 'admin')->pluck('id') as $adminId) {
            SystemNotification::create([
                'user_id' => $adminId,
                'message' => 'User baru telah mendaftar dalam sistem.',
                'status' => 'unread',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('staff.requests.create')->with('success', 'Pendaftaran berjaya.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
