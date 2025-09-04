<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\Program;
use App\Models\User;

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
        
        $query = Subject::with(['program', 'faculty']);
        
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
        $programs = Program::all();
        
        // Get available faculty for assignment
        $availableFaculty = User::whereHas('role', function($query) {
            $query->where('name', 'Faculty');
        })->get();
        
        // Calculate statistics
        $stats = [
            'total' => Subject::count(),
            'active' => Subject::where('is_active', true)->count(),
            'assigned' => Subject::whereNotNull('faculty_id')->count(),
            'unassigned' => Subject::whereNull('faculty_id')->count()
        ];
        
        return view('subjects.index', compact('subjects', 'programs', 'availableFaculty', 'stats'));
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
            'description' => 'nullable|string',
            'units' => 'required|integer|min:1|max:10',
            'program_id' => 'required|exists:programs,id',
            'year_level' => 'required|integer|min:1|max:4',
            'semester' => 'required|in:1st Semester,2nd Semester,Summer'
        ]);
        
        $subject = Subject::create([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'units' => $request->units,
            'program_id' => $request->program_id,
            'year_level' => $request->year_level,
            'semester' => $request->semester,
            'is_active' => true
        ]);
        
        return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        $subject->load(['program', 'faculty']);
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

        $subject->update(['faculty_id' => $request->faculty_id]);

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
