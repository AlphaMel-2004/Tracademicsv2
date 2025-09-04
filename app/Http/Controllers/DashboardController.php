<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ComplianceSubmission;
use App\Models\DocumentType;
use App\Models\Semester;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $user->load(['role', 'department', 'currentSemester']);

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
            'total_submissions' => ComplianceSubmission::count(),
            'pending_submissions' => ComplianceSubmission::where('status', 'pending')->count(),
            'approved_submissions' => ComplianceSubmission::where('status', 'approved')->count(),
            'active_semester' => Semester::where('is_active', true)->first(),
        ];
    }

    /**
     * Get VPAA dashboard data
     */
    private function getVPAAData()
    {
        return [
            'total_submissions' => ComplianceSubmission::count(),
            'pending_reviews' => ComplianceSubmission::where('status', 'submitted')->count(),
            'compliance_rate' => $this->calculateComplianceRate(),
            'active_semester' => Semester::where('is_active', true)->first(),
        ];
    }

    /**
     * Get Dean dashboard data
     */
    private function getDeanData(User $user)
    {
        $departmentSubmissions = ComplianceSubmission::whereHas('user', function ($query) use ($user) {
            $query->where('department_id', $user->department_id);
        });

        return [
            'department_submissions' => $departmentSubmissions->count(),
            'pending_submissions' => $departmentSubmissions->where('status', 'pending')->count(),
            'faculty_count' => User::where('department_id', $user->department_id)
                                  ->where('role_id', 5)->count(), // Faculty role
            'active_semester' => Semester::where('is_active', true)->first(),
        ];
    }

    /**
     * Get Program Head dashboard data
     */
    private function getProgramHeadData(User $user)
    {
        // This would need program assignments - for now, basic data
        return [
            'program_submissions' => ComplianceSubmission::whereHas('user', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })->count(),
            'active_semester' => Semester::where('is_active', true)->first(),
        ];
    }

    /**
     * Get Faculty dashboard data
     */
    private function getFacultyData(User $user)
    {
        $currentSemester = Semester::where('is_active', true)->first();
        
        $mySubmissions = ComplianceSubmission::where('user_id', $user->id)
                                           ->where('semester_id', $currentSemester->id ?? 0);

        return [
            'my_submissions' => $mySubmissions->count(),
            'pending_submissions' => $mySubmissions->where('status', 'pending')->count(),
            'approved_submissions' => $mySubmissions->where('status', 'approved')->count(),
            'document_types' => DocumentType::all(),
            'active_semester' => $currentSemester,
        ];
    }

    /**
     * Calculate overall compliance rate
     */
    private function calculateComplianceRate()
    {
        $totalRequired = DocumentType::where('is_required', true)->count();
        $totalSubmitted = ComplianceSubmission::where('status', '!=', 'pending')->count();
        
        if ($totalRequired > 0) {
            return round(($totalSubmitted / $totalRequired) * 100, 2);
        }
        
        return 0;
    }
}
