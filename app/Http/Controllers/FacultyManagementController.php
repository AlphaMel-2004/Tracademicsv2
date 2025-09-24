<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subject;
use App\Models\FacultyAssignment;
use App\Models\Semester;
use App\Models\ComplianceSubmission;

class FacultyManagementController extends Controller
{
    /**
     * Display faculty management dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only Program Heads and above can access this
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403, 'Unauthorized access');
        }
        
        $faculty = User::where('role_id', function($query) {
            $query->select('id')
                  ->from('roles')
                  ->where('name', 'Faculty');
        })->with(['department', 'role'])->paginate(10);
        
        // Calculate statistics
        $stats = [
            'total' => User::whereHas('role', function($query) {
                $query->where('name', 'Faculty');
            })->count(),
            'active' => User::whereHas('role', function($query) {
                $query->where('name', 'Faculty');
            })->whereNotNull('last_login_at')->count(),
            'pending' => 0, // This would need compliance logic
            'subjects' => 0 // This would need subject assignment logic
        ];
        
        // Sample recent activities
        $recentActivities = [
            [
                'title' => 'New Faculty Registration',
                'description' => 'John Doe registered as new faculty member',
                'time' => '2 hours ago',
                'type' => 'success',
                'status' => 'New'
            ],
            [
                'title' => 'Subject Assignment',
                'description' => 'Mathematics assigned to Jane Smith',
                'time' => '1 day ago',
                'type' => 'info',
                'status' => 'Updated'
            ]
        ];
        
        return view('faculty.index', compact('faculty', 'stats', 'recentActivities'));
    }

    /**
     * Show the form for creating a new faculty member
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only Program Heads and above can access this
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403, 'Unauthorized access');
        }
        
        // For Program Heads, redirect to manage faculty page
        if ($user->role->name === 'Program Head') {
            return redirect()->route('faculty.manage');
        }
        
        return view('faculty.create');
    }

    /**
     * Store a newly created faculty member
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only Program Heads and above can access this
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403, 'Unauthorized access');
        }
        
        // For Program Heads, redirect to register faculty functionality
        if ($user->role->name === 'Program Head') {
            return redirect()->route('faculty.manage');
        }
        
        return redirect()->route('faculty.index');
    }

    /**
     * Display the specified faculty member
     */
    public function show(User $faculty)
    {
        $user = Auth::user();
        
        // Only Program Heads and above can access this
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403, 'Unauthorized access');
        }
        
        // Ensure the user is actually faculty
        if ($faculty->role->name !== 'Faculty') {
            abort(404, 'Faculty member not found');
        }
        
        // For Program Heads, ensure they can only view faculty in their program
        if ($user->role->name === 'Program Head') {
            $facultyAssignments = FacultyAssignment::where('user_id', $faculty->id)
                ->whereHas('subject.program', function($query) use ($user) {
                    $query->where('program_head_id', $user->id);
                })
                ->exists();
                
            if (!$facultyAssignments) {
                abort(403, 'You can only view faculty in your program');
            }
        }
        
        $faculty->load(['department', 'role', 'facultyAssignments.subject', 'complianceSubmissions']);
        
        // For now, redirect to manage faculty page with success message
        return redirect()->route('faculty.manage')->with('info', 'Faculty details for ' . $faculty->name);
    }

    /**
     * Show the form for editing the specified faculty member
     */
    public function edit(User $faculty)
    {
        $user = Auth::user();
        
        // Only Program Heads and above can access this
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403, 'Unauthorized access');
        }
        
        // Ensure the user is actually faculty
        if ($faculty->role->name !== 'Faculty') {
            abort(404, 'Faculty member not found');
        }
        
        // For Program Heads, redirect to manage faculty page
        if ($user->role->name === 'Program Head') {
            return redirect()->route('faculty.manage')->with('info', 'Faculty editing functionality available through manage faculty interface');
        }
        
        return view('faculty.edit', compact('faculty'));
    }

    /**
     * Update the specified faculty member
     */
    public function update(Request $request, User $faculty)
    {
        $user = Auth::user();
        
        // Only Program Heads and above can access this
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403, 'Unauthorized access');
        }
        
        // Ensure the user is actually faculty
        if ($faculty->role->name !== 'Faculty') {
            abort(404, 'Faculty member not found');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $faculty->id,
            'faculty_type' => 'required|in:regular,visiting,part-time',
        ]);
        
        $faculty->update([
            'name' => $request->name,
            'email' => $request->email,
            'faculty_type' => $request->faculty_type,
        ]);
        
        return redirect()->route('faculty.manage')->with('success', 'Faculty member updated successfully');
    }

    /**
     * Show faculty assignment form
     */
    public function showAssignments(User $faculty)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403);
        }
        
        if ($user->role->name === 'Program Head' && $faculty->department_id !== $user->department_id) {
            abort(403, 'You can only manage faculty in your department');
        }
        
        $currentSemester = Semester::where('is_active', true)->first();
        
        // Get subjects for the department
        $subjects = Subject::whereHas('programs.department', function($q) use ($user) {
            if ($user->role->name === 'Program Head' || $user->role->name === 'Dean') {
                $q->where('id', $user->department_id);
            }
        })->get();
        
        // Get current assignments
        $assignments = FacultyAssignment::where('user_id', $faculty->id)
            ->where('semester_id', $currentSemester->id ?? 0)
            ->with('subject')
            ->get();
        
        return view('faculty-management.assignments', compact('faculty', 'subjects', 'assignments', 'currentSemester'));
    }
    
    /**
     * Store faculty assignment
     */
    public function storeAssignment(Request $request, User $faculty)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403);
        }
        
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'semester_id' => 'required|exists:semesters,id'
        ]);
        
        // Check if assignment already exists
        $existingAssignment = FacultyAssignment::where([
            'user_id' => $faculty->id,
            'subject_id' => $request->subject_id,
            'semester_id' => $request->semester_id
        ])->first();
        
        if ($existingAssignment) {
            return back()->with('error', 'Faculty is already assigned to this subject for the selected semester.');
        }
        
        FacultyAssignment::create([
            'user_id' => $faculty->id,
            'subject_id' => $request->subject_id,
            'semester_id' => $request->semester_id,
            'assigned_by' => $user->id,
            'assigned_at' => now()
        ]);
        
        return back()->with('success', 'Faculty assigned to subject successfully.');
    }

    /**
     * Assign multiple subjects to faculty
     */
    public function assignSubjects(Request $request, User $faculty)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403, 'Unauthorized access');
        }
        
        // Ensure the user is actually faculty
        if ($faculty->role->name !== 'Faculty') {
            abort(404, 'Faculty member not found');
        }
        
        $request->validate([
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
            'semester_id' => 'required|exists:semesters,id'
        ]);
        
        foreach ($request->subject_ids as $subjectId) {
            FacultyAssignment::updateOrCreate([
                'user_id' => $faculty->id,
                'subject_id' => $subjectId,
                'semester_id' => $request->semester_id,
            ]);
        }
        
        return back()->with('success', 'Subjects assigned to faculty successfully.');
    }

    /**
     * Remove subject assignment from faculty
     */
    public function removeSubject(User $faculty, Subject $subject)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403, 'Unauthorized access');
        }
        
        // Ensure the user is actually faculty
        if ($faculty->role->name !== 'Faculty') {
            abort(404, 'Faculty member not found');
        }
        
        // Find and remove the assignment
        $assignment = FacultyAssignment::where('user_id', $faculty->id)
            ->where('subject_id', $subject->id)
            ->first();
        
        if ($assignment) {
            $assignment->delete();
            return back()->with('success', 'Subject removed from faculty successfully.');
        }
        
        return back()->with('error', 'Assignment not found.');
    }
    
    /**
     * Remove faculty assignment
     */
    public function removeAssignment(FacultyAssignment $assignment)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403);
        }
        
        if ($user->role->name === 'Program Head' && $assignment->user->department_id !== $user->department_id) {
            abort(403);
        }
        
        $assignment->delete();
        
        return back()->with('success', 'Faculty assignment removed successfully.');
    }
    
    /**
     * View faculty compliance status
     */
    public function facultyCompliance(User $faculty)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403);
        }
        
        if ($user->role->name === 'Program Head' && $faculty->department_id !== $user->department_id) {
            abort(403);
        }
        
        $currentSemester = Semester::where('is_active', true)->first();
        
        $submissions = ComplianceSubmission::where('user_id', $faculty->id)
            ->where('semester_id', $currentSemester->id ?? 0)
            ->with(['documentType', 'subject', 'complianceDocuments', 'complianceLinks'])
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        return view('faculty-management.compliance', compact('faculty', 'submissions', 'currentSemester'));
    }
    
    /**
     * Program Head - Manage Faculty in their Program
     */
    public function manageFaculty()
    {
        $user = Auth::user();
        
        // Only Program Heads can access this
        if ($user->role->name !== 'Program Head') {
            abort(403, 'Unauthorized access');
        }
        
        $program = $user->program;
        if (!$program) {
            abort(404, 'No program assigned to this Program Head');
        }
        
        // Get all faculty members from the same department as the Program Head
        // This includes both faculty with and without subject assignments
        $allFaculty = User::where('department_id', $user->department_id)
            ->whereHas('role', function($query) {
                $query->where('name', 'Faculty');
            })
            ->with(['role', 'facultyAssignments' => function($query) use ($program) {
                // Only load assignments for this specific program
                $query->where('program_id', $program->id);
            }, 'facultyAssignments.subject'])
            ->get();

        return view('faculty-management.manage', compact('program', 'allFaculty', 'user'));
    }
    
    /**
     * Program Head - Register new Faculty User
     */
    public function registerFaculty(Request $request)
    {
        $user = Auth::user();
        
        // Only Program Heads can access this
        if ($user->role->name !== 'Program Head') {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
        
        $program = $user->program;
        if (!$program) {
            return response()->json(['error' => 'No program assigned to this Program Head'], 404);
        }
        
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
        
        try {
            // Get Faculty role
            $facultyRole = \App\Models\Role::where('name', 'Faculty')->first();
            if (!$facultyRole) {
                return response()->json(['error' => 'Faculty role not found'], 500);
            }
            
            // Create the faculty user
            $faculty = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => $facultyRole->id,
                'department_id' => $user->department_id, // Same department as Program Head
                'program_id' => null, // Faculty users don't have direct program assignment
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            // Note: Faculty assignments (subject assignments) will be created separately
            // when the faculty is assigned to specific subjects by the Program Head
            
            return response()->json([
                'success' => true,
                'message' => 'Faculty user registered successfully. You can now assign subjects to this faculty member.',
                'faculty' => [
                    'id' => $faculty->id,
                    'name' => $faculty->name,
                    'email' => $faculty->email,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error creating faculty user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the active status of a faculty member
     */
    public function toggleStatus(Request $request, User $faculty)
    {
        try {
            $user = Auth::user();
            
            // Only Program Heads and above can toggle faculty status
            if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
            
            // Ensure the user being toggled is a faculty member
            if ($faculty->role->name !== 'Faculty') {
                return response()->json(['error' => 'Can only toggle faculty member status'], 400);
            }
            
            // For Program Heads, ensure they can only manage faculty in their program
            if ($user->role->name === 'Program Head') {
                if ($faculty->program_id !== $user->program_id) {
                    return response()->json(['error' => 'You can only manage faculty in your program'], 403);
                }
            }
            
            // Toggle the status
            $faculty->is_active = !$faculty->is_active;
            $faculty->save();
            
            $status = $faculty->is_active ? 'activated' : 'deactivated';
            
            return response()->json([
                'success' => true,
                'message' => "Faculty member {$status} successfully",
                'is_active' => $faculty->is_active
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error updating faculty status: ' . $e->getMessage()
            ], 500);
        }
    }
}
