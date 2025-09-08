<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Program;

class ProgramsManagementController extends Controller
{
    // Show department cards with program counts
    public function index()
    {
        $departments = Department::withCount('programs')->get();
        return view('programs-management.index', compact('departments'));
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
