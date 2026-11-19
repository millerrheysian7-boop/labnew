<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private const ROLES = ['admin', 'staff', 'user'];

    private function ensureAdmin(): void
    {
        abort_unless(Auth::user() && Auth::user()->role === 'admin', 403, 'Only administrators can delete user accounts.');
    }

    public function index()
    {
        $users = User::orderBy('created_at')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = self::ROLES;

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['nullable', Rule::in(self::ROLES)],
        ]);

        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->role = Auth::user()->role === 'admin' && ! empty($validated['role'])
            ? $validated['role']
            : 'user';

        $user->save();

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('users.index')->with('success', 'User account created successfully. A verification link was sent to their email.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = self::ROLES;

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['nullable', Rule::in(self::ROLES)],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (Auth::user()->role === 'admin' && ! empty($validated['role'])) {
            $user->role = $validated['role'];
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User account updated successfully.');
    }

    public function sendVerification(User $user)
    {
        abort_unless(
            Auth::user()->role === 'admin' || $user->id === Auth::id(),
            403,
            'You can only send a verification email to your own account.'
        );

        if ($user->hasVerifiedEmail()) {
            return back()->with('error', 'This email is already verified.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent to ' . $user->email . '. Check the inbox.');
    }

    public function destroy(User $user)
    {
        $this->ensureAdmin();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account while logged in.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User account deleted successfully.');
    }
}
