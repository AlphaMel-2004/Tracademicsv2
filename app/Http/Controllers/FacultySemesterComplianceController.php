<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FacultySemesterCompliance;

class FacultySemesterComplianceController extends Controller
{
    /**
     * Update faculty semester compliance data
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'evidence_link' => 'nullable|url|max:500',
            'self_evaluation_status' => 'nullable|in:Complied,Not Complied',
        ]);

        $compliance = FacultySemesterCompliance::findOrFail($id);
        
        // Ensure the user can only update their own compliance
        if ($compliance->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prepare update data
        $updateData = [];
        
        if ($request->has('evidence_link')) {
            $updateData['evidence_link'] = $request->evidence_link ?? '';
            // Auto-update status to "Complied" if evidence link is provided
            if (!empty($request->evidence_link)) {
                $updateData['self_evaluation_status'] = 'Complied';
            }
        }
        
        if ($request->has('self_evaluation_status')) {
            $updateData['self_evaluation_status'] = $request->self_evaluation_status ?? 'Not Complied';
        }

        $compliance->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Faculty semester compliance updated successfully',
            'data' => $compliance->fresh()->load('documentType'),
            'updated_fields' => $updateData
        ]);
    }
}
