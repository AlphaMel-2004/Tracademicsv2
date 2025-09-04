<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\Program;
use App\Models\User;

class DepartmentManagementController extends Controller
{
    /**
     * Display department management dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only MIS, VPAA, and Dean can access this
        if (!in_array($user->role->name, ['MIS', 'VPAA', 'Dean'])) {
            abort(403, 'Unauthorized access');
        }
        
        $departments = Department::withCount(['programs', 'users'])->paginate(10);
        
        $stats = [
            'total_departments' => Department::count(),
            'total_programs' => Program::count(),
            'total_faculty' => User::whereHas('role', function($q) { $q->where('name', 'Faculty'); })->count(),
            'total_staff' => User::whereHas('role', function($q) { $q->whereIn('name', ['Dean', 'Program Head']); })->count()
        ];
        
        return view('department-management.index', compact('departments', 'stats'));
    }

    /**
     * Show the form for creating a new department
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['MIS', 'VPAA'])) {
            abort(403, 'Unauthorized access');
        }
        
        return view('department-management.create');
    }

    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['MIS', 'VPAA'])) {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'code' => 'required|string|max:20|unique:departments',
            'description' => 'nullable|string'
        ]);
        
        Department::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description
        ]);
        
        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified department
     */
    public function show(Department $department)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['MIS', 'VPAA', 'Dean'])) {
            abort(403, 'Unauthorized access');
        }
        
        $department->load(['programs', 'users.role']);
        
        $departmentStats = [
            'programs_count' => $department->programs->count(),
            'faculty_count' => $department->users->where('role.name', 'Faculty')->count(),
            'staff_count' => $department->users->whereIn('role.name', ['Dean', 'Program Head'])->count(),
            'total_submissions' => \App\Models\ComplianceSubmission::whereHas('user', function($q) use ($department) {
                $q->where('department_id', $department->id);
            })->count()
        ];
        
        return view('department-management.show', compact('department', 'departmentStats'));
    }

    /**
     * Show the form for editing the specified department
     */
    public function edit(Department $department)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['MIS', 'VPAA'])) {
            abort(403, 'Unauthorized access');
        }
        
        return view('department-management.edit', compact('department'));
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, Department $department)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['MIS', 'VPAA'])) {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
            'description' => 'nullable|string'
        ]);
        
        $department->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description
        ]);
        
        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified department
     */
    public function destroy(Department $department)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        // Check if department has programs
        if ($department->programs()->exists()) {
            return back()->with('error', 'Cannot delete department that has programs.');
        }
        
        // Check if department has users
        if ($department->users()->exists()) {
            return back()->with('error', 'Cannot delete department that has users.');
        }
        
        $department->delete();
        
        return back()->with('success', 'Department deleted successfully.');
    }

    /**
     * Manage programs for a department
     */
    public function programs(Department $department)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['MIS', 'VPAA', 'Dean'])) {
            abort(403, 'Unauthorized access');
        }
        
        $programs = $department->programs()->withCount(['subjects', 'users'])->paginate(10);
        
        return view('department-management.programs', compact('department', 'programs'));
    }

    /**
     * Store new program for department
     */
    public function storeProgram(Request $request, Department $department)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['MIS', 'VPAA'])) {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:programs',
            'description' => 'nullable|string'
        ]);
        
        $department->programs()->create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description
        ]);
        
        return back()->with('success', 'Program created successfully.');
    }
}
