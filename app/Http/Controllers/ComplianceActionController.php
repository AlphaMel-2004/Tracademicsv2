<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FacultySemesterCompliance;
use App\Models\SubjectCompliance;

class ComplianceActionController extends Controller
{
    /**
     * Approve semester compliance
     */
    public function approveSemesterCompliance(Request $request, $complianceId)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Program Head') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $compliance = FacultySemesterCompliance::findOrFail($complianceId);
        
        // Verify the faculty belongs to the program head's program
        // Check through faculty assignments since faculty users don't have direct program_id
        $facultyProgramIds = $compliance->user->facultyAssignments()->pluck('program_id')->toArray();
        
        if (!in_array($user->program_id, $facultyProgramIds)) {
            return response()->json(['error' => 'Unauthorized access to this compliance record'], 403);
        }
        
        // Update compliance status to approved
        $compliance->update([
            'approval_status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'review_comments' => $request->input('comments', '')
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Compliance approved successfully',
            'status' => 'approved'
        ]);
    }
    
    /**
     * Reject semester compliance
     */
    public function rejectSemesterCompliance(Request $request, $complianceId)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Program Head') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $compliance = FacultySemesterCompliance::findOrFail($complianceId);
        
        // Verify the faculty belongs to the program head's program
        // Check through faculty assignments since faculty users don't have direct program_id
        $facultyProgramIds = $compliance->user->facultyAssignments()->pluck('program_id')->toArray();
        
        if (!in_array($user->program_id, $facultyProgramIds)) {
            return response()->json(['error' => 'Unauthorized access to this compliance record'], 403);
        }
        
        // Update compliance status to rejected
        $compliance->update([
            'approval_status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'review_comments' => $request->input('comments', '')
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Compliance rejected',
            'status' => 'rejected'
        ]);
    }
    
    /**
     * Approve subject compliance
     */
    public function approveSubjectCompliance(Request $request, $complianceId)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Program Head') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $compliance = SubjectCompliance::findOrFail($complianceId);
        
        // Verify the faculty belongs to the program head's program
        // Check through faculty assignments since faculty users don't have direct program_id
        $facultyProgramIds = $compliance->user->facultyAssignments()->pluck('program_id')->toArray();
        
        if (!in_array($user->program_id, $facultyProgramIds)) {
            return response()->json(['error' => 'Unauthorized access to this compliance record'], 403);
        }
        
        // Update compliance status to approved
        $compliance->update([
            'approval_status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'review_comments' => $request->input('comments', '')
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Subject compliance approved successfully',
            'status' => 'approved'
        ]);
    }
    
    /**
     * Reject subject compliance
     */
    public function rejectSubjectCompliance(Request $request, $complianceId)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'Program Head') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $compliance = SubjectCompliance::findOrFail($complianceId);
        
        // Verify the faculty belongs to the program head's program
        // Check through faculty assignments since faculty users don't have direct program_id
        $facultyProgramIds = $compliance->user->facultyAssignments()->pluck('program_id')->toArray();
        
        if (!in_array($user->program_id, $facultyProgramIds)) {
            return response()->json(['error' => 'Unauthorized access to this compliance record'], 403);
        }
        
        // Update compliance status to rejected
        $compliance->update([
            'approval_status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'review_comments' => $request->input('comments', '')
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Subject compliance rejected',
            'status' => 'rejected'
        ]);
    }
}
