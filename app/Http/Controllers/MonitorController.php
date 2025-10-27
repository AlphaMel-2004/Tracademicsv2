<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Department;
use App\Models\Program;
use App\Models\FacultySemesterCompliance;
use App\Models\SubjectCompliance;
use App\Models\DocumentType;
use App\Models\Semester;

class MonitorController extends Controller
{
    /**
     * VPAA Monitor - Display all departments
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'VPAA') {
            abort(403, 'Unauthorized access');
        }
        
        // Get all departments with compliance statistics
        $departments = Department::with(['programs', 'users'])
            ->get()
            ->map(function ($department) {
                $facultyIds = $department->users()->whereHas('role', function($query) {
                    $query->where('name', 'Faculty');
                })->pluck('id');
                
                // Get submissions from both compliance types
                $semesterSubmissions = FacultySemesterCompliance::whereIn('user_id', $facultyIds)->count();
                $subjectSubmissions = SubjectCompliance::whereIn('user_id', $facultyIds)->count();
                $totalSubmissions = $semesterSubmissions + $subjectSubmissions;
                
                $semesterApproved = FacultySemesterCompliance::whereIn('user_id', $facultyIds)
                    ->where('approval_status', 'approved')->count();
                $subjectApproved = SubjectCompliance::whereIn('user_id', $facultyIds)
                    ->where('approval_status', 'approved')->count();
                $approvedSubmissions = $semesterApproved + $subjectApproved;
                
                $semesterPending = FacultySemesterCompliance::whereIn('user_id', $facultyIds)
                    ->where('approval_status', 'pending')->count();
                $subjectPending = SubjectCompliance::whereIn('user_id', $facultyIds)
                    ->where('approval_status', 'pending')->count();
                $pendingSubmissions = $semesterPending + $subjectPending;
                
                $complianceRate = $totalSubmissions > 0 ? 
                    round(($approvedSubmissions / $totalSubmissions) * 100, 1) : 0;
                
                return [
                    'department' => $department,
                    'total_programs' => $department->programs->count(),
                    'total_faculty' => $facultyIds->count(),
                    'total_submissions' => $totalSubmissions,
                    'approved_submissions' => $approvedSubmissions,
                    'pending_submissions' => $pendingSubmissions,
                    'compliance_rate' => $complianceRate
                ];
            });
        
        // Overall statistics
        $overallStats = [
            'total_users' => User::whereHas('role', function($query) {
                $query->whereIn('name', ['Faculty', 'Program Head', 'Dean']);
            })->count(),
            'total_submissions' => FacultySemesterCompliance::count() + SubjectCompliance::count(),
            'approved_submissions' => FacultySemesterCompliance::where('approval_status', 'approved')->count() + 
                                    SubjectCompliance::where('approval_status', 'approved')->count(),
            'pending_submissions' => FacultySemesterCompliance::where('approval_status', 'pending')->count() + 
                                   SubjectCompliance::where('approval_status', 'pending')->count(),
        ];
        
        $overallStats['compliance_rate'] = $overallStats['total_submissions'] > 0 ? 
            round(($overallStats['approved_submissions'] / $overallStats['total_submissions']) * 100, 1) : 0;
        
        return view('monitor.vpaa', compact('departments', 'overallStats'));
    }
    
    /**
     * VPAA Monitor - Display programs within a department
     */
    public function department(Department $department)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'VPAA') {
            abort(403, 'Unauthorized access');
        }
        
        $programs = $department->programs()->with(['facultyAssignments.user'])
            ->get()
            ->map(function ($program) {
                // Get unique faculty members assigned to this program
                $facultyIds = $program->facultyAssignments()
                    ->whereHas('user.role', function($query) {
                        $query->where('name', 'Faculty');
                    })
                    ->pluck('user_id')
                    ->unique();
                
                $semesterSubmissions = FacultySemesterCompliance::whereIn('user_id', $facultyIds)->count();
                $subjectSubmissions = SubjectCompliance::whereIn('user_id', $facultyIds)->count();
                $totalSubmissions = $semesterSubmissions + $subjectSubmissions;
                
                $semesterApproved = FacultySemesterCompliance::whereIn('user_id', $facultyIds)
                    ->where('approval_status', 'approved')->count();
                $subjectApproved = SubjectCompliance::whereIn('user_id', $facultyIds)
                    ->where('approval_status', 'approved')->count();
                $approvedSubmissions = $semesterApproved + $subjectApproved;
                
                $complianceRate = $totalSubmissions > 0 ? 
                    round(($approvedSubmissions / $totalSubmissions) * 100, 1) : 0;
                
                return [
                    'program' => $program,
                    'total_faculty' => $facultyIds->count(),
                    'total_submissions' => $totalSubmissions,
                    'approved_submissions' => $approvedSubmissions,
                    'compliance_rate' => $complianceRate
                ];
            });
        
        return view('monitor.vpaa-programs', compact('department', 'programs'));
    }
    
    /**
     * VPAA Monitor - Display faculty compliance within a program
     */
    public function programFaculty(Program $program)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'VPAA') {
            abort(403, 'Unauthorized access');
        }
        
        // Get faculty members assigned to this program through faculty assignments
        $facultyIds = $program->facultyAssignments()
            ->whereHas('user.role', function($query) {
                $query->where('name', 'Faculty');
            })
            ->pluck('user_id')
            ->unique();
            
        $faculty = User::whereIn('id', $facultyIds)
            ->with([
                'facultySemesterCompliances.documentType',
                'subjectCompliances.documentType',
                'subjectCompliances.subject',
                'facultyAssignments.subject',
                'facultyAssignments.semester'
            ])
            ->get();

        // Get current semester for semester-wide compliance
        $currentSemester = Semester::where('is_active', true)->first();
        
        // Get document types for each submission type
        $semesterDocTypes = DocumentType::where('submission_type', 'semester')->get();
        $subjectDocTypes = DocumentType::where('submission_type', 'subject')->get();

        $facultyCompliance = $faculty->map(function ($facultyMember) use ($currentSemester, $semesterDocTypes, $subjectDocTypes) {
            // Get semester-wide compliance data
            $semesterCompliances = collect();
            if ($currentSemester) {
                foreach ($semesterDocTypes as $docType) {
                    $compliance = $facultyMember->facultySemesterCompliances()
                        ->where('semester_id', $currentSemester->id)
                        ->where('document_type_id', $docType->id)
                        ->with('documentType', 'programHeadApprover', 'deanApprover')
                        ->first();
                    
                    if (!$compliance) {
                        // Create placeholder for missing compliance
                        $compliance = new FacultySemesterCompliance([
                            'user_id' => $facultyMember->id,
                            'document_type_id' => $docType->id,
                            'semester_id' => $currentSemester->id,
                            'evidence_link' => '',
                            'self_evaluation_status' => 'Not Complied',
                        ]);
                        $compliance->documentType = $docType;
                    }
                    
                    $semesterCompliances->push($compliance);
                }
            }
            
            // Get assigned subjects with subject-specific compliance
            $assignedSubjects = $facultyMember->facultyAssignments()
                ->with(['subject'])
                ->get()
                ->map(function ($assignment) use ($facultyMember, $subjectDocTypes) {
                    $subject = $assignment->subject;
                    
                    // Get subject-specific compliance for each document type
                    $subjectCompliances = collect();
                    foreach ($subjectDocTypes as $docType) {
                        $compliance = $facultyMember->subjectCompliances()
                            ->where('subject_id', $subject->id)
                            ->where('document_type_id', $docType->id)
                            ->with('documentType', 'programHeadApprover', 'deanApprover')
                            ->first();
                        
                        if (!$compliance) {
                            // Create placeholder for missing compliance
                            $compliance = new SubjectCompliance([
                                'user_id' => $facultyMember->id,
                                'subject_id' => $subject->id,
                                'document_type_id' => $docType->id,
                                'evidence_link' => '',
                                'self_evaluation_status' => 'Not Complied',
                            ]);
                            $compliance->documentType = $docType;
                        }
                        
                        $subjectCompliances->push($compliance);
                    }
                    
                    return [
                        'assignment' => $assignment,
                        'subject' => $subject,
                        'compliances' => $subjectCompliances
                    ];
                });
            
            return [
                'faculty' => $facultyMember,
                'semester_compliances' => $semesterCompliances,
                'assigned_subjects' => $assignedSubjects
            ];
        });
        
        return view('monitor.vpaa-faculty', compact('program', 'facultyCompliance', 'currentSemester'));
    }
    
    /**
     * Dean Monitor - Display programs within dean's department
     */
    public function faculty()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Dean') {
            abort(403, 'Unauthorized access');
        }
        
        $department = $user->department;
        if (!$department) {
            abort(404, 'No department assigned to this dean');
        }
        
        $programs = $department->programs()->with(['facultyAssignments.user'])
            ->get()
            ->map(function ($program) {
                // Get unique faculty members assigned to this program
                $facultyIds = $program->facultyAssignments()
                    ->whereHas('user.role', function($query) {
                        $query->where('name', 'Faculty');
                    })
                    ->pluck('user_id')
                    ->unique();
                
                $semesterSubmissions = FacultySemesterCompliance::whereIn('user_id', $facultyIds)->count();
                $subjectSubmissions = SubjectCompliance::whereIn('user_id', $facultyIds)->count();
                $totalSubmissions = $semesterSubmissions + $subjectSubmissions;
                
                $semesterApproved = FacultySemesterCompliance::whereIn('user_id', $facultyIds)
                    ->where('approval_status', 'approved')->count();
                $subjectApproved = SubjectCompliance::whereIn('user_id', $facultyIds)
                    ->where('approval_status', 'approved')->count();
                $approvedSubmissions = $semesterApproved + $subjectApproved;
                
                $complianceRate = $totalSubmissions > 0 ? 
                    round(($approvedSubmissions / $totalSubmissions) * 100, 1) : 0;
                
                return [
                    'program' => $program,
                    'total_faculty' => $facultyIds->count(),
                    'total_submissions' => $totalSubmissions,
                    'approved_submissions' => $approvedSubmissions,
                    'compliance_rate' => $complianceRate
                ];
            });
        
        return view('monitor.dean-faculty', compact('department', 'programs'));
    }
    
    /**
     * Dean Monitor - Display faculty compliance within a program
     */
    public function deanProgramFaculty(Program $program)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Dean') {
            abort(403, 'Unauthorized access');
        }
        
        // Verify program belongs to dean's department
        if ($program->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access to this program');
        }
        
        // Get faculty members assigned to this program through faculty assignments
        $facultyIds = $program->facultyAssignments()
            ->whereHas('user.role', function($query) {
                $query->where('name', 'Faculty');
            })
            ->pluck('user_id')
            ->unique();
            
        $faculty = User::whereIn('id', $facultyIds)
            ->with([
                'facultySemesterCompliances.documentType',
                'subjectCompliances.documentType',
                'subjectCompliances.subject',
                'facultyAssignments.subject',
                'facultyAssignments.semester'
            ])
            ->get();

        // Get current semester for semester-wide compliance
        $currentSemester = Semester::where('is_active', true)->first();
        
        // Get document types for each submission type
        $semesterDocTypes = DocumentType::where('submission_type', 'semester')->get();
        $subjectDocTypes = DocumentType::where('submission_type', 'subject')->get();

        $facultyCompliance = $faculty->map(function ($facultyMember) use ($currentSemester, $semesterDocTypes, $subjectDocTypes) {
            // Get semester-wide compliance data
            $semesterCompliances = collect();
            if ($currentSemester) {
                foreach ($semesterDocTypes as $docType) {
                    $compliance = $facultyMember->facultySemesterCompliances()
                        ->where('semester_id', $currentSemester->id)
                        ->where('document_type_id', $docType->id)
                        ->with('documentType', 'programHeadApprover', 'deanApprover')
                        ->first();
                    
                    if (!$compliance) {
                        // Create placeholder for missing compliance
                        $compliance = new FacultySemesterCompliance([
                            'user_id' => $facultyMember->id,
                            'document_type_id' => $docType->id,
                            'semester_id' => $currentSemester->id,
                            'evidence_link' => '',
                            'self_evaluation_status' => 'Not Complied',
                        ]);
                        $compliance->documentType = $docType;
                    }
                    
                    $semesterCompliances->push($compliance);
                }
            }
            
            // Get assigned subjects with subject-specific compliance
            $assignedSubjects = $facultyMember->facultyAssignments()
                ->with(['subject'])
                ->get()
                ->map(function ($assignment) use ($facultyMember, $subjectDocTypes) {
                    $subject = $assignment->subject;
                    
                    // Get subject-specific compliance for each document type
                    $subjectCompliances = collect();
                    foreach ($subjectDocTypes as $docType) {
                        $compliance = $facultyMember->subjectCompliances()
                            ->where('subject_id', $subject->id)
                            ->where('document_type_id', $docType->id)
                            ->with('documentType')
                            ->first();
                        
                        if (!$compliance) {
                            // Create placeholder for missing compliance
                            $compliance = new SubjectCompliance([
                                'user_id' => $facultyMember->id,
                                'subject_id' => $subject->id,
                                'document_type_id' => $docType->id,
                                'evidence_link' => '',
                                'self_evaluation_status' => 'Not Complied',
                            ]);
                            $compliance->documentType = $docType;
                        }
                        
                        $subjectCompliances->push($compliance);
                    }
                    
                    return [
                        'assignment' => $assignment,
                        'subject' => $subject,
                        'compliances' => $subjectCompliances
                    ];
                });
            
            // Calculate compliance rate for the badge display
            $allCompliances = $semesterCompliances->merge(
                $assignedSubjects->flatMap(function($subjectData) {
                    return $subjectData['compliances'];
                })
            );
            
            $totalCompliances = $allCompliances->count();
            $compliedCount = $allCompliances->where('self_evaluation_status', 'Complied')->count();
            $complianceRate = $totalCompliances > 0 ? 
                round(($compliedCount / $totalCompliances) * 100, 1) : 0;
            
            return [
                'faculty' => $facultyMember,
                'semester_compliances' => $semesterCompliances,
                'assigned_subjects' => $assignedSubjects,
                'compliance_rate' => $complianceRate
            ];
        });
        
        return view('monitor.dean-program-faculty', compact('program', 'facultyCompliance', 'currentSemester'));
    }
    
    /**
     * Program Head Monitor - Display faculty compliance
     */
    public function compliance()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Program Head') {
            abort(403, 'Unauthorized access');
        }
        
        $program = $user->program;
        if (!$program) {
            abort(404, 'No program assigned to this program head');
        }
        
        // Get faculty members assigned to this program through faculty assignments
        $facultyIds = $program->facultyAssignments()
            ->whereHas('user.role', function($query) {
                $query->where('name', 'Faculty');
            })
            ->pluck('user_id')
            ->unique();
            
        $faculty = User::whereIn('id', $facultyIds)
            ->with([
                'facultySemesterCompliances.documentType',
                'subjectCompliances.documentType',
                'subjectCompliances.subject',
                'facultyAssignments.subject',
                'facultyAssignments.semester'
            ])
            ->get();

        // Get current semester for semester-wide compliance
        $currentSemester = Semester::where('is_active', true)->first();
        
        // Get document types for each submission type
        $semesterDocTypes = DocumentType::where('submission_type', 'semester')->get();
        $subjectDocTypes = DocumentType::where('submission_type', 'subject')->get();

        $facultyCompliance = $faculty->map(function ($facultyMember) use ($currentSemester, $semesterDocTypes, $subjectDocTypes) {
            // Get semester-wide compliance data
            $semesterCompliances = collect();
            if ($currentSemester) {
                foreach ($semesterDocTypes as $docType) {
                    $compliance = $facultyMember->facultySemesterCompliances()
                        ->where('semester_id', $currentSemester->id)
                        ->where('document_type_id', $docType->id)
                        ->with('documentType')
                        ->first();
                    
                    if (!$compliance) {
                        // Create placeholder for missing compliance
                        $compliance = new FacultySemesterCompliance([
                            'user_id' => $facultyMember->id,
                            'document_type_id' => $docType->id,
                            'semester_id' => $currentSemester->id,
                            'evidence_link' => '',
                            'self_evaluation_status' => 'Not Complied',
                        ]);
                        $compliance->documentType = $docType;
                    }
                    
                    $semesterCompliances->push($compliance);
                }
            }
            
            // Get assigned subjects with subject-specific compliance
            $assignedSubjects = $facultyMember->facultyAssignments()
                ->with(['subject'])
                ->get()
                ->map(function ($assignment) use ($facultyMember, $subjectDocTypes) {
                    $subject = $assignment->subject;
                    
                    // Get subject-specific compliance for each document type
                    $subjectCompliances = collect();
                    foreach ($subjectDocTypes as $docType) {
                        $compliance = $facultyMember->subjectCompliances()
                            ->where('subject_id', $subject->id)
                            ->where('document_type_id', $docType->id)
                            ->with('documentType')
                            ->first();
                        
                        if (!$compliance) {
                            // Create placeholder for missing compliance
                            $compliance = new SubjectCompliance([
                                'user_id' => $facultyMember->id,
                                'subject_id' => $subject->id,
                                'document_type_id' => $docType->id,
                                'evidence_link' => '',
                                'self_evaluation_status' => 'Not Complied',
                            ]);
                            $compliance->documentType = $docType;
                        }
                        
                        $subjectCompliances->push($compliance);
                    }
                    
                    return [
                        'assignment' => $assignment,
                        'subject' => $subject,
                        'compliances' => $subjectCompliances
                    ];
                });
            
            return [
                'faculty' => $facultyMember,
                'semester_compliances' => $semesterCompliances,
                'assigned_subjects' => $assignedSubjects
            ];
        });
        
        return view('monitor.program-head-compliance', compact('program', 'facultyCompliance', 'currentSemester'));
    }

    /**
     * Approve semester compliance
     */
    public function approveSemesterCompliance(Request $request, $id)
    {
        try {
            $request->validate([
                'comments' => 'nullable|string|max:500'
            ]);

            $compliance = FacultySemesterCompliance::findOrFail($id);
            $user = Auth::user();
            
            // Log for debugging
            Log::info('Approving semester compliance', [
                'user_id' => $user->id,
                'user_role' => $user->role->name,
                'compliance_id' => $id,
                'comments' => $request->comments,
                'compliance_user_id' => $compliance->user_id,
                'user_program_id' => $user->program_id ?? null,
                'user_dept_id' => $user->department_id ?? null,
                'faculty_user_dept_id' => $compliance->user->department_id ?? null
            ]);
            
            // Check permissions
            if ($user->role->name === 'Program Head') {
                // Check if the faculty user is assigned to the Program Head's program
                if (!$user->program_id) {
                    return response()->json(['error' => 'Program Head not assigned to a program'], 403);
                }
                
                // Check if the faculty is assigned to this program head's program
                $facultyProgramIds = $compliance->user->facultyAssignments()->pluck('program_id')->toArray();
                if (!in_array($user->program_id, $facultyProgramIds)) {
                    return response()->json(['error' => 'You can only approve compliance for faculty in your program'], 403);
                }
                
                $compliance->update([
                    'program_head_approval_status' => 'approved',
                    'comments' => $request->comments,
                    'program_head_approved_by' => $user->id,
                    'program_head_approved_at' => now(),
                ]);
                
            } elseif ($user->role->name === 'Dean') {
                // Check if the faculty user is in the Dean's department
                if (!$user->department_id) {
                    return response()->json(['error' => 'Dean not assigned to a department'], 403);
                }
                
                if ($compliance->user->department_id !== $user->department_id) {
                    return response()->json(['error' => 'You can only approve compliance for faculty in your department'], 403);
                }
                
                $compliance->update([
                    'dean_approval_status' => 'approved',
                    'comments' => $request->comments,
                    'dean_approved_by' => $user->id,
                    'dean_approved_at' => now(),
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized - Invalid role: ' . $user->role->name], 403);
            }

            // Update overall approval status if both levels approved
            if ($compliance->program_head_approval_status === 'approved' && $compliance->dean_approval_status === 'approved') {
                $compliance->update(['approval_status' => 'approved']);
            }

            $compliance->refresh();
            $compliance->load(['documentType']);

            return response()->json([
                'success' => true,
                'message' => 'Semester compliance approved successfully',
                'compliance' => $compliance
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving semester compliance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'compliance_id' => $id
            ]);
            
            return response()->json([
                'error' => 'Error processing request: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    /**
     * Reject semester compliance (needs revision)
     */
    public function rejectSemesterCompliance(Request $request, $id)
    {
        try {
            $request->validate([
                'comments' => 'required|string|max:500'
            ]);

            $compliance = FacultySemesterCompliance::findOrFail($id);
            $user = Auth::user();
            
            // Log for debugging
            Log::info('Rejecting semester compliance', [
                'user_id' => $user->id,
                'user_role' => $user->role->name,
                'compliance_id' => $id,
                'comments' => $request->comments,
                'compliance_user_id' => $compliance->user_id,
                'user_program_id' => $user->program_id ?? null,
                'user_dept_id' => $user->department_id ?? null
            ]);
            
            // Check permissions
            if ($user->role->name === 'Program Head') {
                // Check if the faculty user is assigned to the Program Head's program
                if (!$user->program_id) {
                    return response()->json(['error' => 'Program Head not assigned to a program'], 403);
                }
                
                // Check if the faculty is assigned to this program head's program
                $facultyProgramIds = $compliance->user->facultyAssignments()->pluck('program_id')->toArray();
                if (!in_array($user->program_id, $facultyProgramIds)) {
                    return response()->json(['error' => 'You can only approve compliance for faculty in your program'], 403);
                }
                
                $compliance->update([
                    'program_head_approval_status' => 'needs_revision',
                    'comments' => $request->comments,
                    'program_head_approved_by' => $user->id,
                    'program_head_approved_at' => now(),
                    'approval_status' => 'needs_revision'
                ]);
                
            } elseif ($user->role->name === 'Dean') {
                // Check if the faculty user is in the Dean's department
                if (!$user->department_id) {
                    return response()->json(['error' => 'Dean not assigned to a department'], 403);
                }
                
                if ($compliance->user->department_id !== $user->department_id) {
                    return response()->json(['error' => 'You can only approve compliance for faculty in your department'], 403);
                }
                
                $compliance->update([
                    'dean_approval_status' => 'needs_revision',
                    'comments' => $request->comments,
                    'dean_approved_by' => $user->id,
                    'dean_approved_at' => now(),
                    'approval_status' => 'needs_revision'
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized - Invalid role: ' . $user->role->name], 403);
            }

            $compliance->refresh();
            $compliance->load(['documentType']);

            return response()->json([
                'success' => true,
                'message' => 'Semester compliance marked for revision',
                'compliance' => $compliance
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting semester compliance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'compliance_id' => $id
            ]);
            
            return response()->json([
                'error' => 'Error processing request: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    /**
     * Approve subject compliance
     */
    public function approveSubjectCompliance(Request $request, $id)
    {
        try {
            $request->validate([
                'comments' => 'nullable|string|max:500'
            ]);

            $compliance = SubjectCompliance::findOrFail($id);
            $user = Auth::user();
            
            // Log for debugging
            Log::info('Approving subject compliance', [
                'user_id' => $user->id,
                'user_role' => $user->role->name,
                'compliance_id' => $id,
                'comments' => $request->comments,
                'subject_program_id' => $compliance->subject->program_id ?? null,
                'user_program_id' => $user->program_id ?? null,
                'compliance_user_id' => $compliance->user_id,
                'faculty_user_dept_id' => $compliance->user->department_id ?? null,
                'user_dept_id' => $user->department_id ?? null
            ]);
            
            // Check permissions
            if ($user->role->name === 'Program Head') {
                // Check if the subject belongs to the Program Head's program
                if (!$user->program_id) {
                    return response()->json(['error' => 'Program Head not assigned to a program'], 403);
                }
                
                if ($compliance->subject->program_id !== $user->program_id) {
                    return response()->json(['error' => 'You can only approve subjects from your program'], 403);
                }
                
                $compliance->update([
                    'program_head_approval_status' => 'approved',
                    'comments' => $request->comments,
                    'program_head_approved_by' => $user->id,
                    'program_head_approved_at' => now(),
                ]);
                
            } elseif ($user->role->name === 'Dean') {
                // Check if the subject's program belongs to the Dean's department
                if (!$user->department_id) {
                    return response()->json(['error' => 'Dean not assigned to a department'], 403);
                }
                
                if ($compliance->subject->program->department_id !== $user->department_id) {
                    return response()->json(['error' => 'You can only approve subjects from your department'], 403);
                }
                
                $compliance->update([
                    'dean_approval_status' => 'approved',
                    'comments' => $request->comments,
                    'dean_approved_by' => $user->id,
                    'dean_approved_at' => now(),
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized - Invalid role: ' . $user->role->name], 403);
            }

            // Update overall approval status if both levels approved
            if ($compliance->program_head_approval_status === 'approved' && $compliance->dean_approval_status === 'approved') {
                $compliance->update(['approval_status' => 'approved']);
            }

            $compliance->refresh();
            $compliance->load(['documentType', 'subject']);

            return response()->json([
                'success' => true,
                'message' => 'Subject compliance approved successfully',
                'compliance' => $compliance
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving subject compliance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'compliance_id' => $id
            ]);
            
            return response()->json([
                'error' => 'Error processing request: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    /**
     * Reject subject compliance (needs revision)
     */
    public function rejectSubjectCompliance(Request $request, $id)
    {
        try {
            $request->validate([
                'comments' => 'required|string|max:500'
            ]);

            $compliance = SubjectCompliance::findOrFail($id);
            $user = Auth::user();
            
            // Log for debugging
            Log::info('Rejecting subject compliance', [
                'user_id' => $user->id,
                'user_role' => $user->role->name,
                'compliance_id' => $id,
                'comments' => $request->comments,
                'subject_program_id' => $compliance->subject->program_id ?? null,
                'user_program_id' => $user->program_id ?? null
            ]);
            
            // Check permissions
            if ($user->role->name === 'Program Head') {
                // Check if the subject belongs to the Program Head's program
                if (!$user->program_id) {
                    return response()->json(['error' => 'Program Head not assigned to a program'], 403);
                }
                
                if ($compliance->subject->program_id !== $user->program_id) {
                    return response()->json(['error' => 'You can only approve subjects from your program'], 403);
                }
                
                $compliance->update([
                    'program_head_approval_status' => 'needs_revision',
                    'comments' => $request->comments,
                    'program_head_approved_by' => $user->id,
                    'program_head_approved_at' => now(),
                    'approval_status' => 'needs_revision'
                ]);
                
            } elseif ($user->role->name === 'Dean') {
                // Check if the subject's program belongs to the Dean's department
                if (!$user->department_id) {
                    return response()->json(['error' => 'Dean not assigned to a department'], 403);
                }
                
                if ($compliance->subject->program->department_id !== $user->department_id) {
                    return response()->json(['error' => 'You can only approve subjects from your department'], 403);
                }
                
                $compliance->update([
                    'dean_approval_status' => 'needs_revision',
                    'comments' => $request->comments,
                    'dean_approved_by' => $user->id,
                    'dean_approved_at' => now(),
                    'approval_status' => 'needs_revision'
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized - Invalid role: ' . $user->role->name], 403);
            }

            $compliance->refresh();
            $compliance->load(['documentType', 'subject']);

            return response()->json([
                'success' => true,
                'message' => 'Subject compliance marked for revision',
                'compliance' => $compliance
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting subject compliance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'compliance_id' => $id
            ]);
            
            return response()->json([
                'error' => 'Error processing request: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }
}
