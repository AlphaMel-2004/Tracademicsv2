<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FacultySemesterCompliance;
use App\Models\SubjectCompliance;

class ComplianceActionController extends Controller
{
    /**
     * Approve semester compliance by Program Head
     */
    public function approveSemesterCompliance(Request $request, $complianceId)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['Program Head', 'Dean'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $compliance = FacultySemesterCompliance::findOrFail($complianceId);
        
        // Verify the faculty belongs to the user's program (for Program Heads)
        if ($user->role->name === 'Program Head') {
            $facultyProgramIds = $compliance->user->facultyAssignments()->pluck('program_id')->toArray();
            
            if (!in_array($user->program_id, $facultyProgramIds)) {
                return response()->json(['error' => 'Unauthorized access to this compliance record'], 403);
            }
            
            // Program Head approval
            $compliance->update([
                'program_head_approval_status' => 'approved',
                'program_head_approved_by' => $user->id,
                'program_head_approved_at' => now(),
                'program_head_comments' => $request->input('comments', '')
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Compliance approved by Program Head',
                'status' => 'approved_by_program_head'
            ]);
        }
        
        // Dean approval (can only approve if Program Head has approved)
        if ($user->role->name === 'Dean') {
            // Check if Program Head has approved first
            if ($compliance->program_head_approval_status !== 'approved') {
                return response()->json(['error' => 'Program Head must approve first'], 400);
            }
            
            $compliance->update([
                'dean_approval_status' => 'approved',
                'dean_approved_by' => $user->id,
                'dean_approved_at' => now(),
                'dean_comments' => $request->input('comments', ''),
                // Update overall approval status only when Dean approves
                'approval_status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Compliance fully approved by Dean',
                'status' => 'fully_approved'
            ]);
        }
        
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    /**
     * Request revision for semester compliance
     */
    public function rejectSemesterCompliance(Request $request, $complianceId)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['Program Head', 'Dean'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $compliance = FacultySemesterCompliance::findOrFail($complianceId);
        
        if ($user->role->name === 'Program Head') {
            // Verify the faculty belongs to the program head's program
            $facultyProgramIds = $compliance->user->facultyAssignments()->pluck('program_id')->toArray();
            
            if (!in_array($user->program_id, $facultyProgramIds)) {
                return response()->json(['error' => 'Unauthorized access to this compliance record'], 403);
            }
            
            // Program Head requests revision
            $compliance->update([
                'program_head_approval_status' => 'needs_revision',
                'program_head_approved_by' => $user->id,
                'program_head_approved_at' => now(),
                'program_head_comments' => $request->input('comments', ''),
                // Reset overall approval status
                'approval_status' => 'needs_revision',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Compliance marked for revision by Program Head',
                'status' => 'needs_revision'
            ]);
        }
        
        if ($user->role->name === 'Dean') {
            // Dean can request revision regardless of Program Head status
            $compliance->update([
                'dean_approval_status' => 'needs_revision',
                'dean_approved_by' => $user->id,
                'dean_approved_at' => now(),
                'dean_comments' => $request->input('comments', ''),
                // Reset overall approval status
                'approval_status' => 'needs_revision',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Compliance marked for revision by Dean',
                'status' => 'needs_revision'
            ]);
        }
        
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    /**
     * Approve subject compliance
     */
    public function approveSubjectCompliance(Request $request, $complianceId)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['Program Head', 'Dean'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $compliance = SubjectCompliance::findOrFail($complianceId);
        
        if ($user->role->name === 'Program Head') {
            // Verify the subject belongs to the program head's program
            $subject = $compliance->subject;
            
            if ($subject->program_id !== $user->program_id) {
                return response()->json(['error' => 'Unauthorized access to this subject compliance record'], 403);
            }
            
            // Program Head approval (first level)
            $compliance->update([
                'program_head_approval_status' => 'approved',
                'program_head_approved_by' => $user->id,
                'program_head_approved_at' => now(),
                'program_head_comments' => $request->input('comments', ''),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Subject compliance approved by Program Head. Awaiting Dean approval for final approval.',
                'status' => 'approved_by_program_head'
            ]);
        }
        
        if ($user->role->name === 'Dean') {
            // Dean can only approve if Program Head has approved first
            if ($compliance->program_head_approval_status !== 'approved') {
                return response()->json([
                    'error' => 'Subject compliance must be approved by Program Head first',
                    'current_status' => $compliance->program_head_approval_status
                ], 422);
            }
            
            // Dean approval (final approval)
            $compliance->update([
                'dean_approval_status' => 'approved',
                'dean_approved_by' => $user->id,
                'dean_approved_at' => now(),
                'dean_comments' => $request->input('comments', ''),
                // Set final approval status
                'approval_status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Subject compliance fully approved by Dean',
                'status' => 'approved'
            ]);
        }
        
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    /**
     * Request revision for subject compliance
     */
    public function rejectSubjectCompliance(Request $request, $complianceId)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['Program Head', 'Dean'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $compliance = SubjectCompliance::findOrFail($complianceId);
        
        if ($user->role->name === 'Program Head') {
            // Verify the subject belongs to the program head's program
            $subject = $compliance->subject;
            
            if ($subject->program_id !== $user->program_id) {
                return response()->json(['error' => 'Unauthorized access to this subject compliance record'], 403);
            }
            
            // Program Head requests revision
            $compliance->update([
                'program_head_approval_status' => 'needs_revision',
                'program_head_approved_by' => $user->id,
                'program_head_approved_at' => now(),
                'program_head_comments' => $request->input('comments', ''),
                // Reset overall approval status
                'approval_status' => 'needs_revision',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Subject compliance marked for revision by Program Head',
                'status' => 'needs_revision'
            ]);
        }
        
        if ($user->role->name === 'Dean') {
            // Dean can request revision regardless of Program Head status
            $compliance->update([
                'dean_approval_status' => 'needs_revision',
                'dean_approved_by' => $user->id,
                'dean_approved_at' => now(),
                'dean_comments' => $request->input('comments', ''),
                // Reset overall approval status
                'approval_status' => 'needs_revision',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Subject compliance marked for revision by Dean',
                'status' => 'needs_revision'
            ]);
        }
        
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
