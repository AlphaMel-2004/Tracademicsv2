<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Program;
use App\Models\User;
use App\Models\Subject;
use App\Models\FacultyAssignment;
use Illuminate\Support\Facades\DB;

class ProgramsManagementController extends Controller
{
    // Show department cards with comprehensive program management statistics
    public function index()
    {
        // Get departments with comprehensive counts
        $departments = Department::withCount([
            'programs',
            'users as faculty_count' => function($query) {
                $query->whereHas('role', function($roleQuery) {
                    $roleQuery->where('name', 'Faculty');
                });
            },
            'users as program_head_count' => function($query) {
                $query->whereHas('role', function($roleQuery) {
                    $roleQuery->where('name', 'Program Head');
                });
            }
        ])
        ->with(['programs' => function($query) {
            $query->withCount(['subjects', 'facultyAssignments']);
        }])
        ->get()
        ->map(function($department) {
            // Calculate additional metrics
            $department->active_programs_count = $department->programs->count(); // All programs are considered active for now
            $department->total_subjects_count = $department->programs->sum('subjects_count');
            $department->total_faculty_assignments = $department->programs->sum('faculty_assignments_count');
            
            // Calculate program health score (0-100)
            $programsWithFaculty = $department->programs->where('faculty_assignments_count', '>', 0)->count();
            $programsWithSubjects = $department->programs->where('subjects_count', '>', 0)->count();
            $totalPrograms = $department->programs->count();
            
            if ($totalPrograms > 0) {
                $facultyScore = ($programsWithFaculty / $totalPrograms) * 50;
                $subjectScore = ($programsWithSubjects / $totalPrograms) * 50;
                $department->health_score = round($facultyScore + $subjectScore);
            } else {
                $department->health_score = 0;
            }
            
            return $department;
        });

        // Calculate summary statistics
        $totalDepartments = $departments->count();
        $totalPrograms = $departments->sum('programs_count');
        $totalFaculty = $departments->sum('faculty_count');
        $totalSubjects = $departments->sum('total_subjects_count');
        
        // Calculate active departments (departments that are marked as active)
        $activeDepartments = $departments->where('is_active', true)->count();
        
        // Calculate inactive departments
        $inactiveDepartments = $totalDepartments - $activeDepartments;
        
        // Calculate average health score
        $avgHealthScore = $departments->avg('health_score');
        
        return view('programs-management.index', compact(
            'departments', 
            'totalDepartments', 
            'totalPrograms', 
            'activeDepartments',
            'inactiveDepartments', 
            'totalFaculty',
            'totalSubjects',
            'avgHealthScore'
        ));
    }

    // Show programs under a department
    public function department($departmentId)
    {
        $department = Department::with('programs')->findOrFail($departmentId);
        return view('programs-management.department', compact('department'));
    }

    // Show form to add a program to a department
    public function create($departmentId)
    {
        $department = Department::findOrFail($departmentId);
        return view('programs-management.create', compact('department'));
    }

    // Store new program
    public function store(Request $request, $departmentId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:programs,code',
            'description' => 'nullable|string',
        ]);
        $department = Department::findOrFail($departmentId);
        $department->programs()->create($request->only('name', 'code', 'description'));
        return redirect()->route('programs-management.department', $departmentId)->with('success', 'Program added successfully.');
    }
}
