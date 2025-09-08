<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\FacultyAssignment;
use App\Models\DocumentType;
use App\Models\ComplianceSubmission;

class AssignedSubjectsController extends Controller
{
    /**
     * Display subjects assigned to the current faculty member
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Faculty') {
            abort(403, 'Unauthorized access');
        }
        
        // Get subjects assigned to this faculty member
        $assignments = FacultyAssignment::where('user_id', $user->id)
            ->with(['subject', 'semester'])
            ->get();
        
        $subjects = $assignments->map(function ($assignment) use ($user) {
            $subject = $assignment->subject;
            
            // Get document types required for this subject
            $documentTypes = DocumentType::all();
            
            // Get existing submissions for this subject
            $submissions = ComplianceSubmission::where('user_id', $user->id)
                ->where('subject_id', $subject->id)
                ->with('documentType')
                ->get()
                ->keyBy('document_type_id');
            
            $requirements = $documentTypes->map(function ($docType) use ($submissions) {
                $submission = $submissions->get($docType->id);
                
                return [
                    'document_type' => $docType,
                    'submission' => $submission,
                    'status' => $submission ? $submission->status : 'not_submitted',
                    'submitted_at' => $submission ? $submission->submitted_at : null,
                    'review_comments' => $submission ? $submission->review_comments : null
                ];
            });
            
            // Calculate completion percentage
            $totalRequirements = $requirements->count();
            $completedRequirements = $requirements->where('status', 'approved')->count();
            $completionPercentage = $totalRequirements > 0 ? 
                round(($completedRequirements / $totalRequirements) * 100, 1) : 0;
            
            return [
                'assignment' => $assignment,
                'subject' => $subject,
                'requirements' => $requirements,
                'completion_percentage' => $completionPercentage,
                'total_requirements' => $totalRequirements,
                'completed_requirements' => $completedRequirements
            ];
        });
        
        return view('subjects.assigned', compact('subjects'));
    }
    
    /**
     * Display requirements for a specific subject
     */
    public function show(Subject $subject)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Faculty') {
            abort(403, 'Unauthorized access');
        }
        
        // Verify this subject is assigned to the faculty member
        $assignment = FacultyAssignment::where('user_id', $user->id)
            ->where('subject_id', $subject->id)
            ->with('semester')
            ->first();
        
        if (!$assignment) {
            abort(404, 'Subject not assigned to you or does not exist');
        }
        
        // Get document types and existing submissions
        $documentTypes = DocumentType::all();
        $submissions = ComplianceSubmission::where('user_id', $user->id)
            ->where('subject_id', $subject->id)
            ->with('documentType')
            ->get()
            ->keyBy('document_type_id');
        
        $requirements = $documentTypes->map(function ($docType) use ($submissions) {
            $submission = $submissions->get($docType->id);
            
            return [
                'document_type' => $docType,
                'submission' => $submission,
                'status' => $submission ? $submission->status : 'not_submitted',
                'submitted_at' => $submission ? $submission->submitted_at : null,
                'review_comments' => $submission ? $submission->review_comments : null,
                'file_path' => $submission ? $submission->file_path : null,
                'link_url' => $submission ? $submission->link_url : null
            ];
        });
        
        // Calculate completion stats
        $totalRequirements = $requirements->count();
        $completedRequirements = $requirements->where('status', 'approved')->count();
        $pendingRequirements = $requirements->where('status', 'pending')->count();
        $rejectedRequirements = $requirements->where('status', 'rejected')->count();
        $notSubmittedRequirements = $requirements->where('status', 'not_submitted')->count();
        
        $completionPercentage = $totalRequirements > 0 ? 
            round(($completedRequirements / $totalRequirements) * 100, 1) : 0;
        
        $stats = [
            'total_requirements' => $totalRequirements,
            'completed_requirements' => $completedRequirements,
            'pending_requirements' => $pendingRequirements,
            'rejected_requirements' => $rejectedRequirements,
            'not_submitted_requirements' => $notSubmittedRequirements,
            'completion_percentage' => $completionPercentage
        ];
        
        return view('subjects.requirements', compact('subject', 'assignment', 'requirements', 'stats'));
    }
}
