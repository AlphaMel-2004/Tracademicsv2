<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|ends_with:@brokenshire.edu.ph',
            'password' => 'required',
        ], [
            'email.ends_with' => 'Only @brokenshire.edu.ph email addresses are allowed.',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Update last login time
            $user = Auth::user();
            User::where('id', $user->id)->update(['last_login_at' => now()]);
            
            // Load user relationships
            $user->load(['role', 'department', 'currentSemester']);

            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|ends_with:@brokenshire.edu.ph',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.ends_with' => 'Only @brokenshire.edu.ph email addresses are allowed.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 5, // Default to Faculty role
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
