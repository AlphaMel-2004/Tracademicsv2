<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ComplianceSubmission;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\Department;
use App\Models\Program;
use App\Models\Semester;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display compliance dashboard with charts and statistics
     */
    public function dashboard()
    {
        $user = Auth::user();
        $currentSemester = Semester::where('is_active', true)->first();
        
        // Base query for filtering by user role
        $baseQuery = $this->getFilteredQuery($user);
        
        // Get overall statistics
        $stats = [
            'total_submissions' => $baseQuery->count(),
            'approved_submissions' => $baseQuery->where('status', 'approved')->count(),
            'rejected_submissions' => $baseQuery->where('status', 'rejected')->count(),
            'pending_submissions' => $baseQuery->where('status', 'submitted')->count(),
        ];
        
        // Get submission trends over the last 30 days
        $submissionTrends = $baseQuery
            ->where('submitted_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(submitted_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Get document type compliance
        $documentTypeCompliance = DocumentType::withCount([
            'complianceSubmissions' => function($query) use ($baseQuery, $currentSemester) {
                $query->where('semester_id', $currentSemester->id ?? 0)
                      ->where('status', 'approved');
            }
        ])->get();
        
        // Get faculty compliance status (for admins)
        $facultyCompliance = [];
        if (in_array($user->role->name, ['MIS', 'VPAA', 'Dean', 'Program Head'])) {
            $facultyQuery = User::whereHas('role', function($q) {
                $q->where('name', 'Faculty');
            });
            
            if ($user->role->name === 'Dean' || $user->role->name === 'Program Head') {
                $facultyQuery->where('department_id', $user->department_id);
            }
            
            $facultyCompliance = $facultyQuery->withCount([
                'complianceSubmissions' => function($query) use ($currentSemester) {
                    $query->where('semester_id', $currentSemester->id ?? 0)
                          ->where('status', 'approved');
                }
            ])->get();
        }
        
        // Get recent activity
        $recentActivity = $baseQuery->with(['user', 'documentType', 'reviewer'])
            ->latest()
            ->limit(10)
            ->get();
        
        return view('reports.dashboard', compact(
            'stats', 
            'submissionTrends', 
            'documentTypeCompliance', 
            'facultyCompliance', 
            'recentActivity',
            'currentSemester'
        ));
    }
    
    /**
     * Generate detailed compliance report
     */
    public function complianceReport(Request $request)
    {
        $user = Auth::user();
        $currentSemester = Semester::where('is_active', true)->first();
        
        $query = $this->getFilteredQuery($user);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('document_type')) {
            $query->where('document_type_id', $request->document_type);
        }
        
        if ($request->filled('date_from')) {
            $query->where('submitted_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('submitted_at', '<=', $request->date_to);
        }
        
        $submissions = $query->with(['user', 'documentType', 'subject', 'reviewer'])
            ->orderBy('submitted_at', 'desc')
            ->paginate(50);
        
        $documentTypes = DocumentType::all();
        $departments = Department::all();
        
        return view('reports.compliance', compact(
            'submissions', 
            'documentTypes', 
            'departments', 
            'currentSemester'
        ));
    }
    
    /**
     * Export compliance report to Excel/CSV
     */
    public function exportReport(Request $request)
    {
        $user = Auth::user();
        $query = $this->getFilteredQuery($user);
        
        // Apply same filters as compliance report
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('document_type')) {
            $query->where('document_type_id', $request->document_type);
        }
        
        if ($request->filled('date_from')) {
            $query->where('submitted_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('submitted_at', '<=', $request->date_to);
        }
        
        $submissions = $query->with(['user', 'documentType', 'subject', 'reviewer'])->get();
        
        $filename = 'compliance-report-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($submissions) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Faculty Name',
                'Email',
                'Department',
                'Document Type',
                'Subject',
                'Status',
                'Submitted Date',
                'Reviewed Date',
                'Reviewed By',
                'Comments'
            ]);
            
            // CSV Data
            foreach ($submissions as $submission) {
                fputcsv($file, [
                    $submission->user->name,
                    $submission->user->email,
                    $submission->user->department->name ?? 'N/A',
                    $submission->documentType->name,
                    $submission->subject ? $submission->subject->code . ' - ' . $submission->subject->name : 'N/A',
                    ucfirst($submission->status),
                    $submission->submitted_at->format('Y-m-d H:i:s'),
                    $submission->reviewed_at ? $submission->reviewed_at->format('Y-m-d H:i:s') : 'N/A',
                    $submission->reviewer->name ?? 'N/A',
                    $submission->review_comments ?? 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get filtered query based on user role
     */
    private function getFilteredQuery($user)
    {
        $query = ComplianceSubmission::query();
        
        // Filter based on user role
        switch ($user->role->name) {
            case 'Faculty':
                $query->where('user_id', $user->id);
                break;
                
            case 'Dean':
            case 'Program Head':
                $query->whereHas('user', function($q) use ($user) {
                    $q->where('department_id', $user->department_id);
                });
                break;
                
            case 'MIS':
            case 'VPAA':
                // No additional filtering - can see all
                break;
        }
        
        return $query;
    }

    /**
     * Faculty performance report
     */
    public function facultyReport(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['MIS', 'VPAA', 'Dean', 'Program Head'])) {
            abort(403, 'Unauthorized access');
        }
        
        // Get faculty compliance statistics
        $facultyStats = User::whereHas('role', function($q) {
            $q->where('name', 'Faculty');
        })->with(['complianceSubmissions.documentType', 'department'])
        ->get()
        ->map(function($faculty) {
            $totalSubmissions = $faculty->complianceSubmissions->count();
            $approvedSubmissions = $faculty->complianceSubmissions->where('status', 'approved')->count();
            $complianceRate = $totalSubmissions > 0 ? round(($approvedSubmissions / $totalSubmissions) * 100, 2) : 0;
            
            return [
                'faculty' => $faculty,
                'total_submissions' => $totalSubmissions,
                'approved_submissions' => $approvedSubmissions,
                'compliance_rate' => $complianceRate
            ];
        });
        
        return view('reports.faculty', compact('facultyStats'));
    }

    /**
     * Department performance report
     */
    public function departmentReport(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['MIS', 'VPAA', 'Dean'])) {
            abort(403, 'Unauthorized access');
        }
        
        // Get department statistics
        $departmentStats = Department::withCount(['users', 'programs'])
        ->with(['users.complianceSubmissions'])
        ->get()
        ->map(function($department) {
            $totalSubmissions = $department->users->sum(function($user) {
                return $user->complianceSubmissions->count();
            });
            $approvedSubmissions = $department->users->sum(function($user) {
                return $user->complianceSubmissions->where('status', 'approved')->count();
            });
            $complianceRate = $totalSubmissions > 0 ? round(($approvedSubmissions / $totalSubmissions) * 100, 2) : 0;
            
            return [
                'department' => $department,
                'total_submissions' => $totalSubmissions,
                'approved_submissions' => $approvedSubmissions,
                'compliance_rate' => $complianceRate
            ];
        });
        
        return view('reports.department', compact('departmentStats'));
    }
    
    /**
     * Dean Reports Dashboard
     */
    public function deanReports()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Dean') {
            abort(403, 'Unauthorized access');
        }
        
        $department = $user->department;
        if (!$department) {
            abort(404, 'No department assigned to this dean');
        }
        
        // Get programs under this dean's department
        $programs = $department->programs()->with(['users'])->get();
        
        $programStats = $programs->map(function ($program) {
            $facultyIds = $program->users()->whereHas('role', function($query) {
                $query->where('name', 'Faculty');
            })->pluck('id');
            
            $totalSubmissions = ComplianceSubmission::whereIn('user_id', $facultyIds)->count();
            $approvedSubmissions = ComplianceSubmission::whereIn('user_id', $facultyIds)
                ->where('status', 'approved')->count();
            $pendingSubmissions = ComplianceSubmission::whereIn('user_id', $facultyIds)
                ->where('status', 'pending')->count();
            $rejectedSubmissions = ComplianceSubmission::whereIn('user_id', $facultyIds)
                ->where('status', 'rejected')->count();
            
            $complianceRate = $totalSubmissions > 0 ? 
                round(($approvedSubmissions / $totalSubmissions) * 100, 1) : 0;
            
            return [
                'program' => $program,
                'total_faculty' => $facultyIds->count(),
                'total_submissions' => $totalSubmissions,
                'approved_submissions' => $approvedSubmissions,
                'pending_submissions' => $pendingSubmissions,
                'rejected_submissions' => $rejectedSubmissions,
                'compliance_rate' => $complianceRate
            ];
        });
        
        // Department overall stats
        $departmentStats = [
            'total_programs' => $programs->count(),
            'total_faculty' => $department->users()->whereHas('role', function($query) {
                $query->where('name', 'Faculty');
            })->count(),
            'total_submissions' => ComplianceSubmission::whereHas('user', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })->count(),
            'approved_submissions' => ComplianceSubmission::whereHas('user', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })->where('status', 'approved')->count(),
        ];
        
        $departmentStats['compliance_rate'] = $departmentStats['total_submissions'] > 0 ? 
            round(($departmentStats['approved_submissions'] / $departmentStats['total_submissions']) * 100, 1) : 0;
        
        return view('reports.dean', compact('department', 'programStats', 'departmentStats'));
    }
    
    /**
     * Generate Dean PDF Report
     */
    public function generateDeanPDF()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Dean') {
            abort(403, 'Unauthorized access');
        }
        
        $department = $user->department;
        if (!$department) {
            abort(404, 'No department assigned to this dean');
        }
        
        // Get detailed compliance data
        $programs = $department->programs()->with(['users.complianceSubmissions'])->get();
        
        $reportData = [
            'department' => $department,
            'generated_at' => now(),
            'generated_by' => $user->name,
            'programs' => $programs->map(function ($program) {
                $faculty = $program->users()->whereHas('role', function($query) {
                    $query->where('name', 'Faculty');
                })->with('complianceSubmissions')->get();
                
                $facultyData = $faculty->map(function ($facultyMember) {
                    $submissions = $facultyMember->complianceSubmissions;
                    $totalSubmissions = $submissions->count();
                    $approvedSubmissions = $submissions->where('status', 'approved')->count();
                    
                    return [
                        'name' => $facultyMember->name,
                        'email' => $facultyMember->email,
                        'total_submissions' => $totalSubmissions,
                        'approved_submissions' => $approvedSubmissions,
                        'compliance_rate' => $totalSubmissions > 0 ? 
                            round(($approvedSubmissions / $totalSubmissions) * 100, 1) : 0
                    ];
                });
                
                return [
                    'name' => $program->name,
                    'faculty' => $facultyData,
                    'total_faculty' => $facultyData->count(),
                    'avg_compliance_rate' => $facultyData->avg('compliance_rate') ?? 0
                ];
            })
        ];
        
        // Generate PDF using a view
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.dean-pdf', $reportData);
        
        $filename = 'dean-report-' . $department->name . '-' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}
