<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\FacultyAssignment;
use App\Models\DocumentType;
use App\Models\FacultySemesterCompliance;
use App\Models\SubjectCompliance;
use App\Models\Semester;

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
            $currentSemester = Semester::where('is_active', true)->first();
            
            // Get document types required for this subject
            $documentTypes = DocumentType::where('submission_type', 'subject')->get();
            
            // Get existing submissions for this subject
            $submissions = SubjectCompliance::where('user_id', $user->id)
                ->where('subject_id', $subject->id)
                ->where('semester_id', $currentSemester ? $currentSemester->id : null)
                ->with('documentType')
                ->get()
                ->keyBy('document_type_id');
            
            $requirements = $documentTypes->map(function ($docType) use ($submissions) {
                $submission = $submissions->get($docType->id);
                
                return [
                    'document_type' => $docType,
                    'submission' => $submission,
                    'status' => $submission ? $submission->approval_status : 'not_submitted',
                    'submitted_at' => $submission ? $submission->created_at : null,
                    'review_comments' => $submission ? $submission->comments : null
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
        
        // Get current semester and semester-wide compliance data
        $currentSemester = Semester::where('is_active', true)->first();
        $semesterCompliances = collect();
        
        if ($currentSemester) {
            // Get semester document types
            $semesterDocTypes = DocumentType::where('submission_type', 'semester')->get();
            
            // Get existing compliance records for this user and semester
            $existingCompliances = FacultySemesterCompliance::where('user_id', $user->id)
                ->where('semester_id', $currentSemester->id)
                ->with('documentType')
                ->get()
                ->keyBy('document_type_id');
            
            // Create or get compliance records for each semester document type
            foreach ($semesterDocTypes as $docType) {
                if ($existingCompliances->has($docType->id)) {
                    // Use existing compliance record
                    $semesterCompliances->push($existingCompliances->get($docType->id));
                } else {
                    // Create new compliance record
                    $compliance = FacultySemesterCompliance::create([
                        'user_id' => $user->id,
                        'document_type_id' => $docType->id,
                        'semester_id' => $currentSemester->id,
                        'evidence_link' => '',
                        'self_evaluation_status' => 'Not Complied',
                    ]);
                    $compliance->load('documentType');
                    $semesterCompliances->push($compliance);
                }
            }
        }
        
        return view('subjects.assigned', compact('subjects', 'semesterCompliances', 'currentSemester'));
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
        
        // Get only subject-specific document types (exclude semester-wide ones)
        $documentTypes = DocumentType::where('submission_type', 'subject')->get();
        
        // Get existing subject compliance records
        $existingCompliances = SubjectCompliance::where('user_id', $user->id)
            ->where('subject_id', $subject->id)
            ->with('documentType')
            ->get()
            ->keyBy('document_type_id');
        
        // Create or get compliance records for each subject document type
        // Get current active semester
        $currentSemester = Semester::where('is_active', true)->first();
        
        $subjectCompliances = collect();
        foreach ($documentTypes as $docType) {
            if ($existingCompliances->has($docType->id)) {
                // Use existing compliance record
                $subjectCompliances->push($existingCompliances->get($docType->id));
            } else {
                // Create new compliance record
                $compliance = SubjectCompliance::create([
                    'user_id' => $user->id,
                    'subject_id' => $subject->id,
                    'document_type_id' => $docType->id,
                    'semester_id' => $currentSemester ? $currentSemester->id : null,
                    'evidence_link' => '',
                    'self_evaluation_status' => 'Not Complied',
                ]);
                $compliance->load('documentType');
                $subjectCompliances->push($compliance);
            }
        }
        
        // Map compliance records to requirements format
        $requirements = $subjectCompliances->map(function ($compliance) {
            return [
                'compliance' => $compliance,
                'document_type' => $compliance->documentType,
                'submission' => null, // Keep for compatibility but not used in new format
                'status' => $compliance->self_evaluation_status === 'Complied' ? 'approved' : 'not_submitted',
                'submitted_at' => $compliance->updated_at,
                'review_comments' => null,
                'file_path' => null,
                'link_url' => $compliance->evidence_link
            ];
        });
        
        // Calculate completion stats
        $totalRequirements = $requirements->count();
        $completedRequirements = $requirements->where('status', 'approved')->count();
        $pendingRequirements = $requirements->where('status', 'pending')->count();
        $needsRevisionRequirements = $requirements->where('status', 'needs_revision')->count();
        $notSubmittedRequirements = $requirements->where('status', 'not_submitted')->count();
        
        $completionPercentage = $totalRequirements > 0 ? 
            round(($completedRequirements / $totalRequirements) * 100, 1) : 0;
        
        $stats = [
            'total_requirements' => $totalRequirements,
            'completed_requirements' => $completedRequirements,
            'pending_requirements' => $pendingRequirements,
            'needs_revision_requirements' => $needsRevisionRequirements,
            'not_submitted_requirements' => $notSubmittedRequirements,
            'completion_percentage' => $completionPercentage
        ];
        
        return view('subjects.requirements', compact('subject', 'assignment', 'requirements', 'stats'));
    }
}
