<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FacultyManagementController;
use App\Http\Controllers\SubjectManagementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\DepartmentManagementController;

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
});
