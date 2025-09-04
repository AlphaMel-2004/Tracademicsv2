<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;

class UserManagementController extends Controller
{
    /**
     * Display user management dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only MIS can access this
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $users = User::with(['role', 'department'])->paginate(15);
        
        // Calculate statistics
        $stats = [
            'total' => User::count(),
            'active' => User::whereNotNull('last_login_at')
                           ->where('last_login_at', '>', now()->subDays(30))
                           ->count(),
            'mis' => User::whereHas('role', function($q) { $q->where('name', 'MIS'); })->count(),
            'faculty' => User::whereHas('role', function($q) { $q->where('name', 'Faculty'); })->count(),
        ];
        
        return view('user-management.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $roles = Role::all();
        $departments = Department::all();
        
        return view('user-management.create', compact('roles', 'departments'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'faculty_type' => 'nullable|in:regular,visiting,part-time'
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'faculty_type' => $request->faculty_type,
        ]);
        
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $authUser = Auth::user();
        
        if ($authUser->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $user->load(['role', 'department', 'complianceSubmissions']);
        
        return view('user-management.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $authUser = Auth::user();
        
        if ($authUser->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $roles = Role::all();
        $departments = Department::all();
        
        return view('user-management.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $authUser = Auth::user();
        
        if ($authUser->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'faculty_type' => 'nullable|in:regular,visiting,part-time'
        ]);
        
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'faculty_type' => $request->faculty_type,
        ];
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $updateData['password'] = Hash::make($request->password);
        }
        
        $user->update($updateData);
        
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $authUser = Auth::user();
        
        if ($authUser->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        // Prevent deleting own account
        if ($user->id === $authUser->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        // Check if user has compliance submissions
        if ($user->complianceSubmissions()->exists()) {
            return back()->with('error', 'Cannot delete user with existing compliance submissions.');
        }
        
        $user->delete();
        
        return back()->with('success', 'User deleted successfully.');
    }
}
