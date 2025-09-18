@extends('layouts.app')

@section('title', 'Programs Management - TracAdemics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Programs Management</h1>
            <p class="text-muted">Manage academic programs by department</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Programs Management</li>
            </ol>
        </nav>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Total Departments</h5>
                            <h3 class="mb-0">{{ $totalDepartments }}</h3>
                            <small class="opacity-75">{{ $activeDepartments }} active, {{ $inactiveDepartments }} inactive</small>
                        </div>
                        <i class="fas fa-building fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Total Programs</h5>
                            <h3 class="mb-0">{{ $totalPrograms }}</h3>
                            <small class="opacity-75">Across all departments</small>
                        </div>
                        <i class="fas fa-graduation-cap fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Total Faculty</h5>
                            <h3 class="mb-0">{{ $totalFaculty }}</h3>
                            <small class="opacity-75">Active faculty members</small>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Health Score</h5>
                            <h3 class="mb-0">{{ round($avgHealthScore) }}%</h3>
                            <small class="opacity-75">Average program health</small>
                        </div>
                        <i class="fas fa-heartbeat fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Metrics Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Total Subjects</h5>
                            <h3 class="mb-0">{{ $totalSubjects }}</h3>
                            <small class="opacity-75">Across all programs</small>
                        </div>
                        <i class="fas fa-book fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Active Status</h5>
                            <h3 class="mb-0">{{ $activeDepartments > 0 ? round(($activeDepartments / $totalDepartments) * 100) : 0 }}%</h3>
                            <small class="opacity-75">Departments active</small>
                        </div>
                        <i class="fas fa-chart-pie fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="background: linear-gradient(45deg, #6f42c1, #e83e8c); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Avg Faculty/Dept</h5>
                            <h3 class="mb-0">{{ $totalDepartments > 0 ? round($totalFaculty / $totalDepartments, 1) : 0 }}</h3>
                            <small class="opacity-75">Faculty distribution</small>
                        </div>
                        <i class="fas fa-user-graduate fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Grid -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Departments & Programs
            </h5>
        </div>
        <div class="card-body">
            @if($departments->count() > 0)
                <div class="row">
                    @foreach($departments as $department)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-light border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <i class="fas fa-building fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-primary">{{ $department->code }}</h6>
                                            <div class="d-flex gap-1 mt-1">
                                                @if($department->is_active)
                                                    <span class="badge bg-success badge-sm">Active</span>
                                                @else
                                                    <span class="badge bg-secondary badge-sm">Inactive</span>
                                                @endif
                                                @if($department->health_score >= 80)
                                                    <span class="badge bg-success badge-sm">Excellent</span>
                                                @elseif($department->health_score >= 60)
                                                    <span class="badge bg-warning badge-sm">Good</span>
                                                @elseif($department->health_score >= 40)
                                                    <span class="badge bg-orange badge-sm">Fair</span>
                                                @else
                                                    <span class="badge bg-danger badge-sm">Needs Attention</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-primary fw-bold">{{ $department->health_score }}%</div>
                                        <small class="text-muted">Health</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="flex-grow-1">
                                    <h5 class="card-title text-dark mb-2">{{ $department->name }}</h5>
                                    @if($department->description)
                                        <p class="card-text text-muted small mb-3">{{ Str::limit($department->description, 100) }}</p>
                                    @else
                                        <p class="card-text text-muted small mb-3 fst-italic">No description available</p>
                                    @endif
                                    
                                    <!-- Enhanced Statistics Grid -->
                                    <div class="row text-center mb-3">
                                        <div class="col-6 mb-2">
                                            <div class="border-end">
                                                <h4 class="mb-0 text-primary">{{ $department->programs_count }}</h4>
                                                <small class="text-muted">Programs</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <h4 class="mb-0 text-success">{{ $department->faculty_count ?? 0 }}</h4>
                                            <small class="text-muted">Faculty</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="mb-0 text-info">{{ $department->total_subjects_count ?? 0 }}</h4>
                                                <small class="text-muted">Subjects</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="mb-0 text-warning">{{ $department->program_head_count ?? 0 }}</h4>
                                            <small class="text-muted">Heads</small>
                                        </div>
                                    </div>

                                    <!-- Program Health Indicator -->
                                    @if($department->programs_count > 0)
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">Program Health</small>
                                            <small class="text-muted">{{ $department->health_score }}%</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar 
                                                @if($department->health_score >= 80) bg-success
                                                @elseif($department->health_score >= 60) bg-warning
                                                @elseif($department->health_score >= 40) bg-orange
                                                @else bg-danger
                                                @endif" 
                                                role="progressbar" 
                                                style="width: {{ $department->health_score }}%"
                                                aria-valuenow="{{ $department->health_score }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="mt-auto">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('programs-management.department', $department->id) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-2"></i>View Programs
                                        </a>
                                        @if($department->programs_count > 0)
                                            <div class="text-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Updated {{ $department->updated_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    No programs assigned
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Departments Found</h5>
                    <p class="text-muted">No departments are available in the system.</p>
                    <a href="{{ route('departments.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Manage Departments
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .badge-sm {
        font-size: 0.65rem;
        padding: 0.25rem 0.4rem;
    }
    
    .text-primary {
        color: #28a745 !important;
    }
    
    .bg-primary {
        background-color: #28a745 !important;
    }
    
    .btn-primary {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .btn-primary:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    
    .bg-orange {
        background-color: #fd7e14 !important;
    }
</style>
@endsection
