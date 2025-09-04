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
}
