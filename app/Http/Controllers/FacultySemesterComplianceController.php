<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\FacultySemesterCompliance;

class FacultySemesterComplianceController extends Controller
{
    /**
     * Update compliance data
     */
    public function update(Request $request, $id)
    {
        // Debug logging
        Log::info('FacultySemesterCompliance update called', [
            'id' => $id,
            'request_data' => $request->all(),
            'user_id' => Auth::id()
        ]);

        $request->validate([
            'evidence_link' => 'nullable|string|max:500',
            'self_evaluation_status' => 'nullable|in:Complied,Not Complied',
        ]);

        $compliance = FacultySemesterCompliance::findOrFail($id);
        
        // Ensure the user can only update their own compliance
        if ($compliance->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prepare update data
        $updateData = [];
        
        // Handle status updates
        if ($request->has('self_evaluation_status')) {
            $requestedStatus = $request->self_evaluation_status ?? 'Not Complied';
            
            // If user is trying to set status to "Complied" manually, check if evidence link exists
            if ($requestedStatus === 'Complied') {
                $currentLink = $compliance->evidence_link;
                $newLink = $request->has('evidence_link') ? $request->evidence_link : null;
                
                // Only allow "Complied" status if there's an evidence link (current or new)
                if (empty($currentLink) && empty($newLink)) {
                    $updateData['self_evaluation_status'] = 'Not Complied';
                } else {
                    $updateData['self_evaluation_status'] = 'Complied';
                }
            } else {
                $updateData['self_evaluation_status'] = $requestedStatus;
            }
        }
        
        // Handle evidence link (this should take priority over manual status selection)
        if ($request->has('evidence_link')) {
            $updateData['evidence_link'] = $request->evidence_link ?? '';
            // Auto-update status to "Complied" if evidence link is provided (overrides manual selection)
            if (!empty($request->evidence_link)) {
                $updateData['self_evaluation_status'] = 'Complied';
            } else {
                // If evidence link is being removed, set status to "Not Complied"
                $updateData['self_evaluation_status'] = 'Not Complied';
            }
        }

        Log::info('About to update compliance', [
            'compliance_id' => $compliance->id,
            'update_data' => $updateData,
            'before_update' => $compliance->toArray()
        ]);

        $compliance->update($updateData);

        $freshCompliance = $compliance->fresh()->load('documentType');
        
        Log::info('Compliance updated', [
            'after_update' => $freshCompliance->toArray()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Compliance updated successfully',
            'data' => $freshCompliance,
            'updated_fields' => $updateData
        ]);
    }
}
