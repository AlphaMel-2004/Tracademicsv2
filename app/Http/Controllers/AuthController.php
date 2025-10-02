<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Traits\LogsUserActivity;

class AuthController extends Controller
{
    use LogsUserActivity;
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

            // Log the login activity
            $this->logActivity(
                'login',
                'User logged in successfully',
                [
                    'email' => $user->email,
                    'role' => $user->role->name ?? 'Unknown',
                    'remember_me' => $request->boolean('remember')
                ],
                $user->id
            );

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
        $user = Auth::user();
        
        // Log the logout activity
        if ($user) {
            $this->logActivity(
                'logout',
                'User logged out',
                [
                    'email' => $user->email,
                    'role' => $user->role->name ?? 'Unknown'
                ],
                $user->id
            );
        }

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

    /**
     * Show the forgot password form
     */
    public function showForgotPasswordForm()
    {
        // Only MIS (admin) users can use the forgot password form
        // Non-MIS users should contact admin for password reset
        return view('auth.contact-admin');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|ends_with:@brokenshire.edu.ph',
        ], [
            'email.ends_with' => 'Only @brokenshire.edu.ph email addresses are allowed.',
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }

        // Send password reset link
        $status = \Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show the password reset form
     */
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset the given user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|ends_with:@brokenshire.edu.ph',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.ends_with' => 'Only @brokenshire.edu.ph email addresses are allowed.',
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
