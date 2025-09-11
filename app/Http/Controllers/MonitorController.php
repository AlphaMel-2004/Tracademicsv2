<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use App\Models\Program;
use App\Models\ComplianceSubmission;
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
                
                $totalSubmissions = ComplianceSubmission::whereIn('user_id', $facultyIds)->count();
                $approvedSubmissions = ComplianceSubmission::whereIn('user_id', $facultyIds)
                    ->where('status', 'approved')->count();
                $pendingSubmissions = ComplianceSubmission::whereIn('user_id', $facultyIds)
                    ->where('status', 'pending')->count();
                
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
            'total_submissions' => ComplianceSubmission::count(),
            'approved_submissions' => ComplianceSubmission::where('status', 'approved')->count(),
            'pending_submissions' => ComplianceSubmission::where('status', 'pending')->count(),
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
                
                $totalSubmissions = ComplianceSubmission::whereIn('user_id', $facultyIds)->count();
                $approvedSubmissions = ComplianceSubmission::whereIn('user_id', $facultyIds)
                    ->where('status', 'approved')->count();
                
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
            ->with(['complianceSubmissions'])
            ->get();
        
        $facultyCompliance = $faculty->map(function ($facultyMember) {
            $submissions = $facultyMember->complianceSubmissions;
            $totalSubmissions = $submissions->count();
            $approvedSubmissions = $submissions->where('status', 'approved')->count();
            $pendingSubmissions = $submissions->where('status', 'pending')->count();
            $rejectedSubmissions = $submissions->where('status', 'rejected')->count();
            
            $complianceRate = $totalSubmissions > 0 ? 
                round(($approvedSubmissions / $totalSubmissions) * 100, 1) : 0;
            
            return [
                'faculty' => $facultyMember,
                'total_submissions' => $totalSubmissions,
                'approved_submissions' => $approvedSubmissions,
                'pending_submissions' => $pendingSubmissions,
                'rejected_submissions' => $rejectedSubmissions,
                'compliance_rate' => $complianceRate,
                'last_submission' => $submissions->sortByDesc('created_at')->first()
            ];
        });
        
        return view('monitor.vpaa-faculty', compact('program', 'facultyCompliance'));
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
                
                $totalSubmissions = ComplianceSubmission::whereIn('user_id', $facultyIds)->count();
                $approvedSubmissions = ComplianceSubmission::whereIn('user_id', $facultyIds)
                    ->where('status', 'approved')->count();
                
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
            ->with(['complianceSubmissions'])
            ->get();
        
        $facultyCompliance = $faculty->map(function ($facultyMember) {
            $submissions = $facultyMember->complianceSubmissions;
            $totalSubmissions = $submissions->count();
            $approvedSubmissions = $submissions->where('status', 'approved')->count();
            $pendingSubmissions = $submissions->where('status', 'pending')->count();
            $rejectedSubmissions = $submissions->where('status', 'rejected')->count();
            
            $complianceRate = $totalSubmissions > 0 ? 
                round(($approvedSubmissions / $totalSubmissions) * 100, 1) : 0;
            
            return [
                'faculty' => $facultyMember,
                'total_submissions' => $totalSubmissions,
                'approved_submissions' => $approvedSubmissions,
                'pending_submissions' => $pendingSubmissions,
                'rejected_submissions' => $rejectedSubmissions,
                'compliance_rate' => $complianceRate,
                'last_submission' => $submissions->sortByDesc('created_at')->first()
            ];
        });
        
        return view('monitor.dean-program-faculty', compact('program', 'facultyCompliance'));
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
}
