<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Subject;
use App\Models\Program;
use App\Models\User;
use App\Models\FacultyAssignment;
use App\Models\Semester;

class SubjectManagementController extends Controller
{
    /**
     * Display subject management dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only Program Heads and above can access this
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403, 'Unauthorized access');
        }
        
        $query = Subject::with(['program', 'facultyAssignments.user']);
        
        // For Program Heads, filter to show only subjects from their program
        if ($user->role->name === 'Program Head' && $user->program_id) {
            $query->where('program_id', $user->program_id);
        }
        
        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('program')) {
            $query->where('program_id', $request->program);
        }
        
        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }
        
        $subjects = $query->paginate(15);
        
        // Get programs for filter dropdown
        if ($user->role->name === 'Program Head' && $user->program_id) {
            // Program Heads only see their own program
            $programs = Program::where('id', $user->program_id)->get();
        } else {
            $programs = Program::all();
        }
        
        // Get available faculty for assignment
        if ($user->role->name === 'Program Head' && $user->program_id) {
            // For Program Heads: Show all faculty from their department
            // This includes both faculty with and without existing assignments
            $availableFaculty = User::whereHas('role', function($query) {
                $query->where('name', 'Faculty');
            })->where('department_id', $user->department_id)->get();
        } else {
            // For higher roles: Show all faculty
            $availableFaculty = User::whereHas('role', function($query) {
                $query->where('name', 'Faculty');
            })->get();
        }
        
        // Calculate statistics
        $statsQuery = $user->role->name === 'Program Head' && $user->program_id ? 
            Subject::where('program_id', $user->program_id) : Subject::query();
            
        $stats = [
            'total' => $statsQuery->count(),
            'assigned' => DB::table('faculty_assignments')
                ->join('subjects', 'faculty_assignments.subject_id', '=', 'subjects.id')
                ->when($user->role->name === 'Program Head' && $user->program_id, function($q) use ($user) {
                    return $q->where('subjects.program_id', $user->program_id);
                })
                ->distinct('subject_id')
                ->count('subject_id'),
            'unassigned' => 0 // Will be calculated below
        ];
        
        $stats['unassigned'] = $stats['total'] - $stats['assigned'];
        
        return view('subjects.index', compact('subjects', 'programs', 'availableFaculty', 'stats', 'user'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        $programs = Program::all();
        return view('subjects.create', compact('programs'));
    }
    
    /**
     * Store new subject
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403);
        }
        
        $request->validate([
            'code' => 'required|string|max:20|unique:subjects',
            'name' => 'required|string|max:255',
            'units' => 'required|integer|min:1|max:10',
            'program_id' => 'required|exists:programs,id',
            'year_level' => 'required|integer|min:1|max:4'
        ]);
        
        $subject = Subject::create([
            'code' => $request->code,
            'name' => $request->name,
            'units' => $request->units,
            'program_id' => $request->program_id,
            'year_level' => $request->year_level
        ]);
        
        return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        $subject->load(['program', 'facultyAssignments.user']);
        return view('subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {
        $programs = Program::all();
        return view('subjects.edit', compact('subject', 'programs'));
    }
    
    /**
     * Update subject
     */
    public function update(Request $request, Subject $subject)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403);
        }
        
        $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:1|max:10',
            'program_id' => 'required|exists:programs,id',
            'year_level' => 'required|integer|min:1|max:4',
            'semester' => 'required|in:1st Semester,2nd Semester,Summer'
        ]);
        
        $subject->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'units' => $request->units,
            'program_id' => $request->program_id,
            'year_level' => $request->year_level,
            'semester' => $request->semester
        ]);
        
        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
    }

    /**
     * Assign faculty to subject
     */
    public function assign(Request $request, Subject $subject)
    {
        $request->validate([
            'faculty_id' => 'required|exists:users,id'
        ]);

        // Verify the user is faculty
        $faculty = User::find($request->faculty_id);
        if ($faculty->role->name !== 'Faculty') {
            return back()->with('error', 'Selected user is not a faculty member.');
        }

        // Get the active semester
        $activeSemester = Semester::where('is_active', true)->first();
        if (!$activeSemester) {
            return back()->with('error', 'No active semester found.');
        }

        // Check if faculty is already assigned to this subject
        $existingAssignment = FacultyAssignment::where('user_id', $request->faculty_id)
            ->where('subject_id', $subject->id)
            ->where('semester_id', $activeSemester->id)
            ->first();

        if ($existingAssignment) {
            return back()->with('error', 'Faculty is already assigned to this subject.');
        }

        // Create faculty assignment
        FacultyAssignment::create([
            'user_id' => $request->faculty_id,
            'subject_id' => $subject->id,
            'semester_id' => $activeSemester->id,
            'program_id' => $subject->program_id,
        ]);

        return back()->with('success', 'Faculty assigned successfully.');
    }
    
    /**
     * Delete subject
     */
    public function destroy(Subject $subject)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['Program Head', 'Dean', 'VPAA', 'MIS'])) {
            abort(403);
        }
        
        // Check if subject has any compliance submissions
        if ($subject->complianceSubmissions()->exists()) {
            return back()->with('error', 'Cannot delete subject that has compliance submissions.');
        }
        
        $subject->delete();
        
        return back()->with('success', 'Subject deleted successfully.');
    }
}
