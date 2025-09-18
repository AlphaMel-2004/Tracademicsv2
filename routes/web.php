<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ComplianceController;
// use App\Http\Controllers\ComplianceActionController; // Temporarily disabled
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FacultyManagementController;
use App\Http\Controllers\SubjectManagementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\DepartmentManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\AssignedSubjectsController;
use App\Http\Controllers\ProgramsManagementController;
use App\Http\Controllers\FacultySemesterComplianceController;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'passwordSettings'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // System status page (for testing/demonstration)
    Route::get('/system-status', function () {
        return view('system-status');
    })->name('system.status');
    
    // API Routes for AJAX calls
    Route::prefix('api')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'getNotifications']);
        Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead']);
    });
    
    // Compliance routes
    Route::prefix('compliance')->name('compliance.')->group(function () {
        // Document submission routes
        Route::get('/submit', [ComplianceController::class, 'create'])->name('create');
        Route::post('/submit', [ComplianceController::class, 'store'])->name('store');
        
        // My submissions
        Route::get('/my-submissions', [ComplianceController::class, 'mySubmissions'])->name('my-submissions');
        Route::put('/submissions/{submission}', [ComplianceController::class, 'update'])->name('update');
        
        // User submissions (for Program Heads and above)
        Route::get('/user/{user}/submissions', [ComplianceController::class, 'userSubmissions'])->name('user-submissions');
        
        // File and link management
        Route::delete('/documents/{document}', [ComplianceController::class, 'deleteFile'])->name('delete-file');
        Route::delete('/links/{link}', [ComplianceController::class, 'deleteLink'])->name('delete-link');
        
        // Review routes (for admins)
        Route::get('/review', [ComplianceController::class, 'reviewSubmissions'])->name('review');
        Route::post('/review/{submission}', [ComplianceController::class, 'reviewAction'])->name('review-action');
    });
    
    // Faculty Management routes (for Program Head and above)
    Route::middleware(['auth'])->prefix('faculty-management')->name('faculty.')->group(function () {
        Route::get('/', [FacultyManagementController::class, 'index'])->name('index');
        Route::get('/create', [FacultyManagementController::class, 'create'])->name('create');
        Route::post('/', [FacultyManagementController::class, 'store'])->name('store');
        
        // Program Head Faculty Management routes (must be before dynamic routes)
        Route::get('/manage', [FacultyManagementController::class, 'manageFaculty'])->name('manage');
        Route::post('/register', [FacultyManagementController::class, 'registerFaculty'])->name('register');
        Route::post('/{faculty}/toggle-status', [FacultyManagementController::class, 'toggleStatus'])->name('toggle-status');
        
        Route::get('/{faculty}', [FacultyManagementController::class, 'show'])->name('show');
        Route::get('/{faculty}/edit', [FacultyManagementController::class, 'edit'])->name('edit');
        Route::put('/{faculty}', [FacultyManagementController::class, 'update'])->name('update');
        Route::get('/{faculty}/assignments', [FacultyManagementController::class, 'showAssignments'])->name('assignments');
        Route::post('/{faculty}/assign-subjects', [FacultyManagementController::class, 'assignSubjects'])->name('assign-subjects');
        Route::delete('/{faculty}/remove-subject/{subject}', [FacultyManagementController::class, 'removeSubject'])->name('remove-subject');
        Route::post('/{faculty}/assignments', [FacultyManagementController::class, 'storeAssignment'])->name('assignments.store');
        Route::delete('/assignments/{assignment}', [FacultyManagementController::class, 'removeAssignment'])->name('assignments.remove');
        Route::get('/{faculty}/compliance', [FacultyManagementController::class, 'facultyCompliance'])->name('compliance');
    });
    
    // Subject Management routes (for Program Head and above)
    Route::middleware(['auth'])->prefix('subject-management')->name('subjects.')->group(function () {
        Route::get('/', [SubjectManagementController::class, 'index'])->name('index');
        Route::get('/create', [SubjectManagementController::class, 'create'])->name('create');
        Route::post('/', [SubjectManagementController::class, 'store'])->name('store');
        Route::get('/{subject}', [SubjectManagementController::class, 'show'])->name('show');
        Route::get('/{subject}/edit', [SubjectManagementController::class, 'edit'])->name('edit');
        Route::put('/{subject}', [SubjectManagementController::class, 'update'])->name('update');
        Route::post('/{subject}/assign', [SubjectManagementController::class, 'assign'])->name('assign');
        Route::delete('/{subject}', [SubjectManagementController::class, 'destroy'])->name('destroy');
    });

    // Reports routes (for MIS, VPAA, Dean, Program Head)
    Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/compliance', [ReportController::class, 'complianceReport'])->name('compliance');
        Route::get('/faculty', [ReportController::class, 'facultyReport'])->name('faculty');
        Route::get('/department', [ReportController::class, 'departmentReport'])->name('department');
        Route::get('/export/{type}', [ReportController::class, 'exportReport'])->name('export');
    });

    // User Management routes (MIS only)
    Route::middleware(['auth'])->prefix('user-management')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
    });

    // System Settings routes (MIS only)
    Route::middleware(['auth'])->prefix('system-settings')->name('settings.')->group(function () {
        Route::get('/', [SystemSettingsController::class, 'index'])->name('index');
        Route::get('/semesters', [SystemSettingsController::class, 'semesters'])->name('semesters');
        Route::post('/semesters', [SystemSettingsController::class, 'storeSemester'])->name('semesters.store');
        Route::post('/semesters/{semester}/activate', [SystemSettingsController::class, 'activateSemester'])->name('semesters.activate');
        Route::get('/document-types', [SystemSettingsController::class, 'documentTypes'])->name('document-types');
        Route::post('/document-types', [SystemSettingsController::class, 'storeDocumentType'])->name('document-types.store');
        Route::put('/document-types/{documentType}', [SystemSettingsController::class, 'updateDocumentType'])->name('document-types.update');
        Route::delete('/document-types/{documentType}', [SystemSettingsController::class, 'destroyDocumentType'])->name('document-types.destroy');
        Route::get('/user-logs', [SystemSettingsController::class, 'userLogs'])->name('user-logs');
    });

    // Department Management routes (MIS, VPAA, Dean)
    Route::middleware(['auth'])->prefix('department-management')->name('departments.')->group(function () {
        Route::get('/', [DepartmentManagementController::class, 'index'])->name('index');
        Route::get('/create', [DepartmentManagementController::class, 'create'])->name('create');
        Route::post('/', [DepartmentManagementController::class, 'store'])->name('store');
        Route::get('/{department}', [DepartmentManagementController::class, 'show'])->name('show');
        Route::get('/{department}/edit', [DepartmentManagementController::class, 'edit'])->name('edit');
        Route::put('/{department}', [DepartmentManagementController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentManagementController::class, 'destroy'])->name('destroy');
        Route::get('/{department}/programs', [DepartmentManagementController::class, 'programs'])->name('programs');
        Route::post('/{department}/programs', [DepartmentManagementController::class, 'storeProgram'])->name('programs.store');
    });
    
    // Monitor routes (VPAA, Dean, Program Head)
    Route::prefix('monitor')->name('monitor.')->group(function () {
        // VPAA Monitor Routes
        Route::get('/', [MonitorController::class, 'index'])->name('index');
        Route::get('/department/{department}', [MonitorController::class, 'department'])->name('department');
        Route::get('/program/{program}/faculty', [MonitorController::class, 'programFaculty'])->name('program.faculty');
        
        // Dean Monitor Routes
        Route::get('/faculty', [MonitorController::class, 'faculty'])->name('faculty');
        Route::get('/dean/program/{program}/faculty', [MonitorController::class, 'deanProgramFaculty'])->name('dean.program.faculty');
        
        // Program Head Monitor Routes
        Route::get('/compliance', [MonitorController::class, 'compliance'])->name('compliance');
        
        // Compliance Approval Routes (for Program Heads and Deans)
        Route::post('/semester-compliance/{id}/approve', [MonitorController::class, 'approveSemesterCompliance'])->name('semester.compliance.approve');
        Route::post('/semester-compliance/{id}/needs-revision', [MonitorController::class, 'rejectSemesterCompliance'])->name('semester.compliance.needs_revision');
        Route::post('/subject-compliance/{id}/approve', [MonitorController::class, 'approveSubjectCompliance'])->name('subject.compliance.approve');
        Route::post('/subject-compliance/{id}/needs-revision', [MonitorController::class, 'rejectSubjectCompliance'])->name('subject.compliance.needs_revision');
    });
    
    // Faculty Assigned Subjects routes
    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/assigned', [AssignedSubjectsController::class, 'index'])->name('assigned');
        Route::get('/assigned/{subject}', [AssignedSubjectsController::class, 'show'])->name('assigned.show');
    });
    
    // Dean Reports routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/dean', [ReportController::class, 'deanReports'])->name('dean');
        Route::get('/dean/pdf', [ReportController::class, 'generateDeanPDF'])->name('dean.pdf');
    });
    
    // Programs Management routes (MIS only)
    Route::middleware(['auth'])->prefix('programs-management')->name('programs-management.')->group(function () {
        Route::get('/', [ProgramsManagementController::class, 'index'])->name('index');
        Route::get('/department/{department}', [ProgramsManagementController::class, 'department'])->name('department');
        Route::get('/department/{department}/create', [ProgramsManagementController::class, 'create'])->name('create');
        Route::post('/department/{department}', [ProgramsManagementController::class, 'store'])->name('store');
    });
});

// Faculty Semester Compliance routes
Route::middleware(['auth'])->group(function () {
    // Faculty Semester Compliance Update Route
    Route::put('/faculty-compliance/{id}', [\App\Http\Controllers\FacultySemesterComplianceController::class, 'update'])->name('faculty-compliance.update');
    Route::put('/subject-compliance/{id}', [\App\Http\Controllers\SubjectComplianceController::class, 'update'])->name('subject-compliance.update');
    
    // Redirect old semester requirements route to subjects page
    Route::get('/faculty/semester-requirements', function () {
        return redirect()->route('subjects.assigned');
    })->name('faculty.semester-requirements');
});
