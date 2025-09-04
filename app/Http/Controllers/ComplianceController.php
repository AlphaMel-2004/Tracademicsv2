<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ComplianceSubmission;
use App\Models\ComplianceDocument;
use App\Models\ComplianceLink;
use App\Models\DocumentType;
use App\Models\Subject;
use App\Models\Semester;

class ComplianceController extends Controller
{
    /**
     * Display submission form for a document type
     */
    public function create(Request $request)
    {
        // If no document type is specified, show document type selection
        if (!$request->has('document_type_id')) {
            $documentTypes = DocumentType::all();
            return view('compliance.select-document-type', compact('documentTypes'));
        }
        
        $documentType = DocumentType::findOrFail($request->document_type_id);
        $user = Auth::user();
        $activeSemester = Semester::where('is_active', true)->first();
        
        // Get subjects for subject-specific submissions
        $subjects = collect();
        if ($documentType->submission_type === 'subject') {
            $subjects = Subject::whereHas('facultyAssignments', function ($query) use ($user, $activeSemester) {
                $query->where('user_id', $user->id)
                      ->where('semester_id', $activeSemester->id ?? 0);
            })->get();
        }

        return view('compliance.create', compact('documentType', 'subjects', 'activeSemester'));
    }

    /**
     * Store a new compliance submission
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $activeSemester = Semester::where('is_active', true)->first();
        
        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'files.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
            'links.*.url' => 'nullable|url',
            'links.*.title' => 'nullable|string|max:255',
            'links.*.description' => 'nullable|string|max:500',
        ]);

        $documentType = DocumentType::findOrFail($request->document_type_id);

        // Check if submission already exists
        $existingSubmission = ComplianceSubmission::where([
            'user_id' => $user->id,
            'document_type_id' => $documentType->id,
            'semester_id' => $activeSemester->id,
            'subject_id' => $request->subject_id
        ])->first();

        if ($existingSubmission) {
            return back()->with('error', 'You have already submitted this document. You can update your existing submission.');
        }

        // Create submission
        $submission = ComplianceSubmission::create([
            'user_id' => $user->id,
            'document_type_id' => $documentType->id,
            'semester_id' => $activeSemester->id,
            'subject_id' => $request->subject_id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('compliance/' . $user->id . '/' . $activeSemester->id, $filename, 'public');

                ComplianceDocument::create([
                    'submission_id' => $submission->id,
                    'filename' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now(),
                ]);
            }
        }

        // Handle links
        if ($request->has('links')) {
            foreach ($request->links as $linkData) {
                if (!empty($linkData['url'])) {
                    ComplianceLink::create([
                        'submission_id' => $submission->id,
                        'url' => $linkData['url'],
                        'title' => $linkData['title'] ?? null,
                        'description' => $linkData['description'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('compliance.my-submissions')
                        ->with('success', 'Document submitted successfully!');
    }

    /**
     * Display user's submissions
     */
    public function mySubmissions()
    {
        $user = Auth::user();
        $activeSemester = Semester::where('is_active', true)->first();
        
        $submissions = ComplianceSubmission::with(['documentType', 'subject', 'complianceDocuments', 'complianceLinks'])
            ->where('user_id', $user->id)
            ->where('semester_id', $activeSemester->id ?? 0)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('compliance.my-submissions', compact('submissions', 'activeSemester'));
    }

    /**
     * Update an existing submission
     */
    public function update(Request $request, ComplianceSubmission $submission)
    {
        // Verify ownership
        if ($submission->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'files.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'links.*.url' => 'nullable|url',
            'links.*.title' => 'nullable|string|max:255',
            'links.*.description' => 'nullable|string|max:500',
        ]);

        // Update submission status if it was rejected
        if ($submission->status === 'rejected') {
            $submission->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'review_comments' => null,
            ]);
        }

        // Handle new file uploads
        if ($request->hasFile('files')) {
            $user = Auth::user();
            $activeSemester = Semester::where('is_active', true)->first();
            
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('compliance/' . $user->id . '/' . $activeSemester->id, $filename, 'public');

                ComplianceDocument::create([
                    'submission_id' => $submission->id,
                    'filename' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now(),
                ]);
            }
        }

        // Handle new links
        if ($request->has('links')) {
            foreach ($request->links as $linkData) {
                if (!empty($linkData['url'])) {
                    ComplianceLink::create([
                        'submission_id' => $submission->id,
                        'url' => $linkData['url'],
                        'title' => $linkData['title'] ?? null,
                        'description' => $linkData['description'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('compliance.my-submissions')
                        ->with('success', 'Submission updated successfully!');
    }

    /**
     * Delete a file from submission
     */
    public function deleteFile(ComplianceDocument $document)
    {
        // Verify ownership
        if ($document->submission->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete file from storage
        Storage::disk('public')->delete($document->file_path);
        
        // Delete database record
        $document->delete();

        return back()->with('success', 'File deleted successfully!');
    }

    /**
     * Delete a link from submission
     */
    public function deleteLink(ComplianceLink $link)
    {
        // Verify ownership
        if ($link->submission->user_id !== Auth::id()) {
            abort(403);
        }

        $link->delete();

        return back()->with('success', 'Link deleted successfully!');
    }

    /**
     * Display submissions for review (for admins)
     */
    public function reviewSubmissions(Request $request)
    {
        $user = Auth::user();
        
        // Check if user can review submissions
        if (!in_array($user->role->name, ['MIS', 'VPAA', 'Dean', 'Program Head'])) {
            abort(403);
        }

        $query = ComplianceSubmission::with(['user', 'documentType', 'subject', 'semester'])
                    ->where('status', '!=', 'pending');

        // Filter by role permissions
        if ($user->role->name === 'Dean') {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        } elseif ($user->role->name === 'Program Head') {
            // Program heads can only review their program's submissions
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        $submissions = $query->orderBy('submitted_at', 'desc')->paginate(20);

        return view('compliance.review', compact('submissions'));
    }

    /**
     * Approve or reject a submission
     */
    public function reviewAction(Request $request, ComplianceSubmission $submission)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!in_array($user->role->name, ['MIS', 'VPAA', 'Dean', 'Program Head'])) {
            abort(403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
        ]);

        $status = $request->action === 'approve' ? 'approved' : 'rejected';

        $submission->update([
            'status' => $status,
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
            'review_comments' => $request->comments,
        ]);

        $message = $request->action === 'approve' 
            ? 'Submission approved successfully!' 
            : 'Submission rejected successfully!';

        return back()->with('success', $message);
    }
}
