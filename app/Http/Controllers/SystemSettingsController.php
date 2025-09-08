<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Semester;
use App\Models\DocumentType;
use App\Models\SemesterSession;
use App\Models\UserLog;

class SystemSettingsController extends Controller
{
    /**
     * Display system settings dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only MIS can access this
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $activeSemester = Semester::where('is_active', true)->first();
        $semesters = Semester::orderBy('created_at', 'desc')->take(5)->get();
        $documentTypes = DocumentType::all();
        
        $stats = [
            'total_semesters' => Semester::count(),
            'total_document_types' => DocumentType::count(),
            'active_sessions' => SemesterSession::count(),
            'system_users' => \App\Models\User::count()
        ];
        
        return view('system-settings.index', compact('activeSemester', 'semesters', 'documentTypes', 'stats'));
    }

    /**
     * Manage semesters
     */
    public function semesters()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $activeSemester = Semester::with('semesterSessions')->where('is_active', true)->first();
        $semesters = Semester::with('semesterSessions')->orderBy('start_date', 'desc')->paginate(10);
        
        return view('system-settings.semesters', compact('semesters', 'activeSemester'));
    }

    /**
     * Store new semester
     */
    public function storeSemester(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'academic_year' => 'required|string|max:20'
        ]);
        
        // Deactivate current active semester if making this one active
        if ($request->has('is_active')) {
            Semester::where('is_active', true)->update(['is_active' => false]);
        }
        
        Semester::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'academic_year' => $request->academic_year,
            'is_active' => $request->has('is_active')
        ]);
        
        return back()->with('success', 'Semester created successfully.');
    }

    /**
     * Manage document types
     */
    public function documentTypes()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $documentTypes = DocumentType::paginate(10);
        
        return view('system-settings.document-types', compact('documentTypes'));
    }

    /**
     * Store new document type
     */
    public function storeDocumentType(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'submission_type' => 'required|in:semester,subject',
            'is_required' => 'boolean',
            'due_days' => 'required|integer|min:1|max:365'
        ]);
        
        DocumentType::create([
            'name' => $request->name,
            'description' => $request->description,
            'submission_type' => $request->submission_type,
            'is_required' => $request->has('is_required'),
            'due_days' => $request->due_days
        ]);
        
        return back()->with('success', 'Document type created successfully.');
    }

    /**
     * Update document type
     */
    public function updateDocumentType(Request $request, DocumentType $documentType)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'submission_type' => 'required|in:semester,subject',
            'is_required' => 'boolean',
            'due_days' => 'required|integer|min:1|max:365'
        ]);
        
        $documentType->update([
            'name' => $request->name,
            'description' => $request->description,
            'submission_type' => $request->submission_type,
            'is_required' => $request->has('is_required'),
            'due_days' => $request->due_days
        ]);
        
        return back()->with('success', 'Document type updated successfully.');
    }

    /**
     * Delete document type
     */
    public function destroyDocumentType(DocumentType $documentType)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        // Check if document type has submissions
        if ($documentType->complianceSubmissions()->exists()) {
            return back()->with('error', 'Cannot delete document type that has submissions.');
        }
        
        $documentType->delete();
        
        return back()->with('success', 'Document type deleted successfully.');
    }

    /**
     * Activate semester
     */
    public function activateSemester(Semester $semester)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        // Deactivate all semesters
        Semester::where('is_active', true)->update(['is_active' => false]);
        
        // Activate selected semester
        $semester->update(['is_active' => true]);
        
        return back()->with('success', 'Semester activated successfully.');
    }

    /**
     * Manage user logs
     */
    public function userLogs(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'MIS') {
            abort(403, 'Unauthorized access');
        }
        
        $query = UserLog::with(['user.role'])->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $userLogs = $query->paginate(20)->withQueryString();
        
        return view('system-settings.user-logs', compact('userLogs'));
    }
}
