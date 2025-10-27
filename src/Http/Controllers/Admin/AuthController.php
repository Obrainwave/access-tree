<?php

namespace Obrainwave\AccessTree\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('accesstree.admin.dashboard');
        }

        return view('accesstree::admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('accesstree.admin.dashboard'))
                ->with('success', 'Welcome to AccessTree Admin!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('accesstree.admin.login')
            ->with('success', 'You have been logged out successfully.');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('accesstree.admin.dashboard');
        }

        return view('accesstree::admin.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign admin role if it exists
        $adminRole = \Obrainwave\AccessTree\Models\Role::where('slug', 'admin_interface')->first();
        if ($adminRole) {
            $user->roles()->attach($adminRole->id);
        }

        Auth::login($user);

        return redirect()->route('accesstree.admin.dashboard')
            ->with('success', 'Account created successfully! Welcome to AccessTree Admin.');
    }
}
