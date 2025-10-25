<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Traits\LogsUserActivity;

class ProfileController extends Controller
{
    use LogsUserActivity;

    /**
     * Show the profile settings page
     */
    public function show()
    {
        return view('profile.show', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $originalName = $user->name;

        User::where('id', $user->id)->update([
            'name' => $request->name,
        ]);

        if ($originalName !== $request->name) {
            self::logActivity(
                'profile_update',
                'Updated profile information',
                [
                    'fields' => [
                        'name' => [
                            'old' => $originalName,
                            'new' => $request->name,
                        ],
                    ],
                ]
            );
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the password settings page
     */
    public function passwordSettings()
    {
        return view('profile.password');
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        User::where('id', $user->id)->update([
            'password' => Hash::make($request->password),
        ]);

        self::logActivity(
            'password_change',
            'Updated account password'
        );

        return redirect()->route('profile.password')->with('success', 'Password updated successfully!');
    }
}
