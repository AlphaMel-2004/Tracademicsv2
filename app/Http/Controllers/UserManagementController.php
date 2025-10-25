<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Traits\LogsUserActivity;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    use LogsUserActivity;
    /**
     * Display user management dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only MIS can access this
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }

        $filters = [
            'search' => $request->input('search'),
            'role' => $request->input('role'),
            'department' => $request->input('department'),
            'status' => $request->input('status'),
        ];

        $activeUserIds = DB::table('sessions')
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->values()
            ->map(fn ($id) => (int) $id)
            ->all();

        $userQuery = User::query()->with(['role', 'department']);

        if ($filters['search'] !== null && $filters['search'] !== '') {
            $search = $filters['search'];
            $userQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('id', $search);
            });
        }

        if (!empty($filters['role'])) {
            $userQuery->where('role_id', $filters['role']);
        }

        if (!empty($filters['department'])) {
            $userQuery->where('department_id', $filters['department']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                if (empty($activeUserIds)) {
                    $userQuery->whereRaw('1 = 0');
                } else {
                    $userQuery->whereIn('id', $activeUserIds);
                }
            } elseif ($filters['status'] === 'inactive') {
                if (!empty($activeUserIds)) {
                    $userQuery->whereNotIn('id', $activeUserIds);
                }
            }
        }

        $users = $userQuery->orderBy('name')->paginate(15)->appends($request->query());
        
        // Get roles and departments for the modal - exclude Faculty role as MIS cannot create faculty users
        $roles = Role::where('name', '!=', 'Faculty')->get();
        $departments = Department::with('programs')->get();
        $filterRoles = Role::orderBy('name')->get();
        
        // Calculate statistics
        $stats = [
            'total' => User::count(),
            'active' => count($activeUserIds),
            'mis' => User::whereHas('role', function($q) { $q->where('name', 'MIS'); })->count(),
            'faculty' => User::whereHas('role', function($q) { $q->where('name', 'Faculty'); })->count(),
        ];
        
        return view('user-management.index', compact('users', 'stats', 'roles', 'departments', 'filterRoles', 'filters', 'activeUserIds'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        // Redirect to index page since we're using a modal
        return redirect()->route('users.index');
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
        
        // Check if trying to create Faculty user - MIS cannot create Faculty users
        $selectedRole = Role::find($request->role_id);
        if ($selectedRole && $selectedRole->name === 'Faculty') {
            return back()->withErrors([
                'role_id' => 'MIS cannot create Faculty users. Faculty users must be registered by Program Heads.'
            ])->withInput();
        }
        
        $departmentId = $request->department_id;
        $programId = $request->program_id;

        $facultyTypeRule = 'nullable|in:regular,part-time';
        if ($selectedRole && $selectedRole->name === 'Faculty') {
            $facultyTypeRule = 'required|in:regular,part-time';
        }

        if ($selectedRole) {
            if (in_array($selectedRole->name, ['MIS', 'VPAA'])) {
                $departmentId = null;
            }

            if ($selectedRole->name === 'Dean') {
                $programId = null;
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|ends_with:@brokenshire.edu.ph',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ],
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'program_id' => 'nullable|exists:programs,id',
            'faculty_type' => $facultyTypeRule
        ], [
            'email.ends_with' => 'Email must be a @brokenshire.edu.ph address.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.'
        ]);

        $facultyType = null;
        if ($selectedRole) {
            if ($selectedRole->name === 'Program Head') {
                $facultyType = 'regular';
            } elseif ($selectedRole->name === 'Faculty') {
                $facultyType = $request->faculty_type;
            }
        }
        
        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'department_id' => $departmentId,
            'program_id' => $programId,
            'faculty_type' => $facultyType,
        ]);
        
        // Log user creation activity
        $this->logActivity(
            'create',
            'Created new user: ' . $newUser->name,
            [
                'created_user_id' => $newUser->id,
                'created_user_email' => $newUser->email,
                'created_user_role' => Role::find($request->role_id)->name ?? 'Unknown',
                'created_user_department' => $request->department_id ? Department::find($request->department_id)->name : null,
                'faculty_type' => $facultyType
            ]
        );
        
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
        
        // MIS cannot edit users with Faculty role or assign Faculty role
        $roles = Role::where('name', '!=', 'Faculty')->get();
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
        
        // Prevent MIS from assigning Faculty role
        $selectedRole = Role::find($request->role_id);
        if ($selectedRole && $selectedRole->name === 'Faculty') {
            return redirect()->back()->withErrors(['role_id' => 'MIS cannot assign Faculty role to users.'])->withInput();
        }
        
        $facultyTypeRule = 'nullable|in:regular,part-time';
        if ($selectedRole && $selectedRole->name === 'Faculty') {
            $facultyTypeRule = 'required|in:regular,part-time';
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'faculty_type' => $facultyTypeRule,
            'password' => 'nullable|string|min:8|confirmed'
        ]);
        
        $departmentId = $request->department_id;
        $programId = $request->program_id;

        if ($selectedRole) {
            if (in_array($selectedRole->name, ['MIS', 'VPAA'])) {
                $departmentId = null;
            }

            if ($selectedRole->name === 'Dean') {
                $programId = null;
            }
        }

        $facultyType = null;
        if ($selectedRole) {
            if ($selectedRole->name === 'Program Head') {
                $facultyType = 'regular';
            } elseif ($selectedRole->name === 'Faculty') {
                $facultyType = $request->faculty_type;
            }
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'department_id' => $departmentId,
            'faculty_type' => $facultyType,
        ];

        if (array_key_exists('program_id', $user->getAttributes())) {
            $updateData['program_id'] = $programId;
        }

        // Handle password update
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        // Handle active status (email verification)
        if ($request->has('is_active')) {
            if (!$user->email_verified_at) {
                $updateData['email_verified_at'] = now();
            }
        } else {
            $updateData['email_verified_at'] = null;
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

    /**
     * Reset user password to default "password"
     */
    public function resetPassword(User $user)
    {
        $authUser = Auth::user();
        
        // Only MIS can reset passwords
        if ($authUser->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        // Prevent resetting own password (MIS should use forgot password form)
        if ($user->id === $authUser->id) {
            return back()->with('error', 'You cannot reset your own password. Use the forgot password form.');
        }
        
        // Reset password to default "password"
        $user->update([
            'password' => Hash::make('password')
        ]);
        
        // Log the activity
        $this->logActivity(
            'Password Reset',
            "Reset password for user: {$user->name} ({$user->email})",
            [
                'target_user_id' => $user->id,
                'target_user_name' => $user->name,
                'target_user_email' => $user->email,
                'reset_by' => $authUser->name
            ],
            $user->id
        );
        
        return back()->with('success', "Password reset successfully for {$user->name}. New password is: password");
    }
}
