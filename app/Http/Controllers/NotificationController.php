<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ComplianceSubmission;
use App\Models\DocumentType;
use App\Models\Semester;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get notifications for the current user
     */
    public function getNotifications()
    {
        $user = Auth::user();
        $notifications = [];

        // Check for upcoming deadlines
        $currentSemester = Semester::where('is_active', true)->first();
        $notifications = [];
        
        if ($currentSemester) {
            $documentTypes = DocumentType::all();
            
            foreach ($documentTypes as $docType) {
                // Calculate deadline based on semester start + due_days
                $deadline = Carbon::parse($currentSemester->start_date)->addDays($docType->due_days);
                
                // Check if deadline is within the next 7 days
                if ($deadline >= now() && $deadline <= now()->addDays(7)) {
                    // Check if user has already submitted this document
                    $hasSubmitted = ComplianceSubmission::where('user_id', $user->id)
                        ->where('document_type_id', $docType->id)
                        ->where('semester_id', $currentSemester->id)
                        ->exists();

                    if (!$hasSubmitted) {
                        $daysLeft = now()->diffInDays($deadline);
                        $notifications[] = [
                            'type' => $daysLeft <= 2 ? 'urgent' : 'warning',
                            'title' => 'Deadline Approaching',
                            'message' => "'{$docType->name}' is due in {$daysLeft} day(s)",
                            'action' => route('compliance.create', ['document_type_id' => $docType->id]),
                            'action_text' => 'Submit Now',
                            'deadline' => $deadline
                        ];
                    }
                }
            }
        }

        // Check for submissions that need revision
        $needsRevisionSubmissions = ComplianceSubmission::where('user_id', $user->id)
            ->where('status', 'needs_revision')
            ->where('reviewed_at', '>=', now()->subDays(7))
            ->with('documentType')
            ->get();

        foreach ($needsRevisionSubmissions as $submission) {
            $notifications[] = [
                'type' => 'danger',
                'title' => 'Submission Needs Revision',
                'message' => "Your '{$submission->documentType->name}' submission needs revision",
                'action' => route('compliance.my-submissions'),
                'action_text' => 'View & Resubmit',
                'reviewed_at' => $submission->reviewed_at
            ];
        }

        // For admins: Check for pending reviews
        if (in_array($user->role->name, ['MIS', 'VPAA', 'Dean', 'Program Head'])) {
            $pendingCount = ComplianceSubmission::where('status', 'submitted')
                ->when($user->role->name === 'Dean', function($q) use ($user) {
                    $q->whereHas('user', function($subQ) use ($user) {
                        $subQ->where('department_id', $user->department_id);
                    });
                })
                ->when($user->role->name === 'Program Head', function($q) use ($user) {
                    $q->whereHas('user', function($subQ) use ($user) {
                        $subQ->where('department_id', $user->department_id);
                    });
                })
                ->count();

            if ($pendingCount > 0) {
                $notifications[] = [
                    'type' => 'info',
                    'title' => 'Pending Reviews',
                    'message' => "You have {$pendingCount} submission(s) awaiting review",
                    'action' => route('compliance.review'),
                    'action_text' => 'Review Now',
                    'count' => $pendingCount
                ];
            }
        }

        return response()->json([
            'notifications' => $notifications,
            'count' => count($notifications)
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAsRead()
    {
        // This would typically update a notifications table
        // For now, we'll just return success
        return response()->json(['success' => true]);
    }
}
