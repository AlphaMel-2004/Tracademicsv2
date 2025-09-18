<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\FacultySemesterCompliance;
use App\Models\SubjectCompliance;
use App\Models\DocumentType;
use App\Models\Semester;
use App\Models\UserLog;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // Get user with relationships loaded
        $user = Auth::user();

        // Role-based dashboard data
        $dashboardData = $this->getDashboardData($user);

        return view('dashboard.index', compact('user', 'dashboardData'));
    }

    /**
     * Get dashboard data based on user role
     */
    private function getDashboardData(User $user)
    {
        switch ($user->role->name) {
            case 'MIS':
                return $this->getMISData();
            case 'VPAA':
                return $this->getVPAAData();
            case 'Dean':
                return $this->getDeanData($user);
            case 'Program Head':
                return $this->getProgramHeadData($user);
            case 'Faculty':
                return $this->getFacultyData($user);
            default:
                return [];
        }
    }

    /**
     * Get MIS dashboard data
     */
    private function getMISData()
    {
        return [
            'total_users' => User::count(),
            'total_submissions' => FacultySemesterCompliance::count() + SubjectCompliance::count(),
            'pending_submissions' => FacultySemesterCompliance::where('approval_status', 'pending')->count() + SubjectCompliance::where('approval_status', 'pending')->count(),
            'approved_submissions' => FacultySemesterCompliance::where('approval_status', 'approved')->count() + SubjectCompliance::where('approval_status', 'approved')->count(),
            'active_semester' => Semester::where('is_active', true)->first(),
            'recent_activities' => UserLog::with(['user.role'])
                                         ->orderBy('created_at', 'desc')
                                         ->limit(10)
                                         ->get(),
        ];
    }

    /**
     * Get VPAA dashboard data
     */
    private function getVPAAData()
    {
        // Get all departments with their compliance analytics
        $departments = \App\Models\Department::all();
        $departmentAnalytics = [];

        foreach ($departments as $department) {
            // Get all compliances from faculty in this department
            $departmentSubmissions = collect()
                ->merge(FacultySemesterCompliance::whereHas('user', function ($query) use ($department) {
                    $query->where('department_id', $department->id);
                })->get())
                ->merge(SubjectCompliance::whereHas('user', function ($query) use ($department) {
                    $query->where('department_id', $department->id);
                })->get());

            $departmentAnalytics[] = [
                'department_name' => $department->name,
                'department_code' => $department->code,
                'total_submissions' => $departmentSubmissions->count(),
                'approved_submissions' => $departmentSubmissions->where('approval_status', 'approved')->count(),
                'pending_submissions' => $departmentSubmissions->where('approval_status', 'pending')->count(),
                'needs_revision_submissions' => $departmentSubmissions->where('approval_status', 'needs_revision')->count(),
                'submitted_submissions' => $departmentSubmissions->where('approval_status', 'submitted')->count(),
                'faculty_count' => User::where('department_id', $department->id)
                                      ->whereHas('role', function($query) {
                                          $query->where('name', 'Faculty');
                                      })->count(),
            ];
        }

        return [
            'total_submissions' => FacultySemesterCompliance::count() + SubjectCompliance::count(),
            'pending_reviews' => FacultySemesterCompliance::where('approval_status', 'submitted')->count() + SubjectCompliance::where('approval_status', 'submitted')->count(),
            'compliance_rate' => $this->calculateComplianceRate(),
            'active_semester' => Semester::where('is_active', true)->first(),
            'department_analytics' => $departmentAnalytics,
        ];
    }

    /**
     * Get Dean dashboard data
     */
    private function getDeanData(User $user)
    {
        $departmentSubmissions = collect()
            ->merge(FacultySemesterCompliance::whereHas('user', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })->get())
            ->merge(SubjectCompliance::whereHas('user', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })->get());

        // Get program analytics for the dean's department
        $programs = \App\Models\Program::where('department_id', $user->department_id)->get();
        $programAnalytics = [];

        foreach ($programs as $program) {
            // Get faculty assigned to subjects in this program through FacultyAssignments
            $facultyInProgram = \App\Models\FacultyAssignment::whereHas('subject', function($query) use ($program) {
                $query->where('program_id', $program->id);
            })->pluck('user_id')->unique();

            // Get compliances from faculty assigned to this program
            $programSubmissions = collect()
                ->merge(FacultySemesterCompliance::whereIn('user_id', $facultyInProgram)->get())
                ->merge(SubjectCompliance::whereIn('user_id', $facultyInProgram)->get());

            $programAnalytics[] = [
                'program_name' => $program->name,
                'program_code' => $program->code,
                'total_submissions' => $programSubmissions->count(),
                'approved_submissions' => $programSubmissions->where('approval_status', 'approved')->count(),
                'pending_submissions' => $programSubmissions->where('approval_status', 'pending')->count(),
                'needs_revision_submissions' => $programSubmissions->where('approval_status', 'needs_revision')->count(),
            ];
        }

        return [
            'department_submissions' => $departmentSubmissions->count(),
            'pending_submissions' => $departmentSubmissions->where('approval_status', 'pending')->count(),
            'faculty_count' => User::where('department_id', $user->department_id)
                                  ->whereHas('role', function($query) {
                                      $query->where('name', 'Faculty');
                                  })->count(),
            'active_semester' => Semester::where('is_active', true)->first(),
            'program_analytics' => $programAnalytics,
        ];
    }

    /**
     * Get Program Head dashboard data
     */
    private function getProgramHeadData(User $user)
    {
        if (!$user->program_id) {
            return [
                'faculty_submitted_documents' => 0,
                'faculty_assigned' => 0,
                'program_subjects' => 0,
                'pending_approvals' => 0,
                'approved_submissions' => 0,
                'needs_revision' => 0,
                'compliance_analytics' => [],
                'active_semester' => Semester::where('is_active', true)->first(),
            ];
        }

        // Get faculty assigned to this program head's program
        $facultyInProgram = \App\Models\FacultyAssignment::where('program_id', $user->program_id)
            ->pluck('user_id')
            ->unique();

        // Get all compliance submissions from faculty in this program
        $semesterCompliances = FacultySemesterCompliance::whereIn('user_id', $facultyInProgram)->get();
        $subjectCompliances = SubjectCompliance::whereIn('user_id', $facultyInProgram)->get();
        $allCompliances = $semesterCompliances->merge($subjectCompliances);

        // Get subjects in this program
        $programSubjects = \App\Models\Subject::where('program_id', $user->program_id)->count();

        // Get unique faculty count assigned to this program
        $facultyAssigned = $facultyInProgram->count();

        // Get compliance analytics
        $complianceAnalytics = [
            'pending' => $allCompliances->where('program_head_approval_status', 'pending')->count(),
            'approved' => $allCompliances->where('program_head_approval_status', 'approved')->count(),
            'needs_revision' => $allCompliances->where('program_head_approval_status', 'needs_revision')->count(),
        ];

        // Calculate monthly compliance trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthSubmissions = $allCompliances->filter(function($compliance) use ($month) {
                return $compliance->created_at && $compliance->created_at->format('Y-m') === $month->format('Y-m');
            })->count();
            
            $monthlyTrend[] = [
                'month' => $month->format('M Y'),
                'submissions' => $monthSubmissions
            ];
        }

        return [
            'faculty_submitted_documents' => $allCompliances->count(),
            'faculty_assigned' => $facultyAssigned,
            'program_subjects' => $programSubjects,
            'pending_approvals' => $complianceAnalytics['pending'],
            'approved_submissions' => $complianceAnalytics['approved'],
            'needs_revision' => $complianceAnalytics['needs_revision'],
            'compliance_analytics' => $complianceAnalytics,
            'monthly_trend' => $monthlyTrend,
            'active_semester' => Semester::where('is_active', true)->first(),
        ];
    }

    /**
     * Get Faculty dashboard data
     */
    private function getFacultyData(User $user)
    {
        $currentSemester = Semester::where('is_active', true)->first();
        
        if (!$currentSemester) {
            return [
                'my_submissions' => 0,
                'pending_submissions' => 0,
                'approved_submissions' => 0,
                'needs_revision_submissions' => 0,
                'under_review_submissions' => 0,
                'document_types' => DocumentType::all(),
                'active_semester' => null,
                'my_recent_submissions' => collect(),
                'upcoming_deadlines' => collect(),
                'compliance_rate' => 0,
                'my_subjects' => collect(),
                'semester_documents' => collect(),
                'subject_documents' => collect(),
            ];
        }

        // Get faculty semester compliances
        $semesterCompliances = FacultySemesterCompliance::where('user_id', $user->id)
                                                        ->where('semester_id', $currentSemester->id)
                                                        ->get();

        // Get subject compliances
        $subjectCompliances = SubjectCompliance::where('user_id', $user->id)
                                              ->where('semester_id', $currentSemester->id)
                                              ->get();

        // Merge all compliances
        $allCompliances = $semesterCompliances->merge($subjectCompliances);

        // Get recent submissions (last 5)
        $recentSubmissions = $allCompliances->sortByDesc('created_at')->take(5);

        // Get user's subjects (if faculty assignments exist)
        $mySubjects = \App\Models\FacultyAssignment::where('user_id', $user->id)
                                                 ->where('semester_id', $currentSemester->id)
                                                 ->with(['subject'])
                                                 ->get();

        // Calculate compliance rate
        $totalRequired = DocumentType::where('is_required', true)->count();
        $submitted = $allCompliances->where('approval_status', 'approved')->count();
        $complianceRate = $totalRequired > 0 ? round(($submitted / $totalRequired) * 100) : 0;

        // Get document types categorized
        $documentTypes = DocumentType::all();
        $semesterDocs = $documentTypes->where('submission_type', 'semester');
        $subjectDocs = $documentTypes->where('submission_type', 'subject');

        // Calculate upcoming deadlines (simplified for now)
        $upcomingDeadlines = $documentTypes->where('is_required', true)->take(3);

        return [
            'my_submissions' => $allCompliances->count(),
            'pending_submissions' => $allCompliances->where('approval_status', 'pending')->count(),
            'approved_submissions' => $allCompliances->where('approval_status', 'approved')->count(),
            'needs_revision_submissions' => $allCompliances->where('approval_status', 'needs_revision')->count(),
            'under_review_submissions' => $allCompliances->where('approval_status', 'submitted')->count(),
            'document_types' => $documentTypes,
            'active_semester' => $currentSemester,
            'my_recent_submissions' => $recentSubmissions,
            'upcoming_deadlines' => $upcomingDeadlines,
            'compliance_rate' => $complianceRate,
            'my_subjects' => $mySubjects,
            'semester_documents' => $semesterDocs,
            'subject_documents' => $subjectDocs,
        ];
    }

    /**
     * Calculate overall compliance rate
     */
    private function calculateComplianceRate()
    {
        $totalRequired = DocumentType::where('is_required', true)->count();
        
        // Count approved submissions from both compliance types
        $semesterApproved = FacultySemesterCompliance::where('approval_status', 'approved')->count();
        $subjectApproved = SubjectCompliance::where('approval_status', 'approved')->count();
        $totalSubmitted = $semesterApproved + $subjectApproved;
        
        if ($totalRequired > 0) {
            return round(($totalSubmitted / $totalRequired) * 100, 2);
        }
        
        return 0;
    }
}
