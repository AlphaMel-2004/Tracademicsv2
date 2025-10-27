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
            'evidence_link' => [
                'nullable',
                'url',
                'max:500',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && !$this->isValidGoogleDriveUrl($value)) {
                        $fail('The evidence link must be a valid Google Drive or Google Docs URL.');
                    }
                },
            ],
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

    /**
     * Determine if the provided URL is a valid Google Drive/Docs link.
     */
    private function isValidGoogleDriveUrl(string $url): bool
    {
        $parsed = parse_url($url);

        if (!$parsed || empty($parsed['host'])) {
            return false;
        }

        $host = strtolower($parsed['host']);
        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        if (!in_array($host, ['drive.google.com', 'docs.google.com'], true)) {
            return false;
        }

        $path = $parsed['path'] ?? '';
        $patterns = [
            '#^/file/d/([a-zA-Z0-9_-]+)(/|$)#',
            '#^/document/d/([a-zA-Z0-9_-]+)(/|$)#',
            '#^/spreadsheets/d/([a-zA-Z0-9_-]+)(/|$)#',
            '#^/presentation/d/([a-zA-Z0-9_-]+)(/|$)#',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        // Handle open?id= and uc?id= formats
        if (in_array($path, ['/open', '/uc'], true) && !empty($parsed['query'])) {
            parse_str($parsed['query'], $query);
            if (!empty($query['id']) && preg_match('#^[a-zA-Z0-9_-]+$#', $query['id'])) {
                return true;
            }
        }

        return false;
    }
}
