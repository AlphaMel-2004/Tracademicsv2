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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Total Departments</h5>
                            <h3 class="mb-0">{{ $departments->count() }}</h3>
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
                            <h3 class="mb-0">{{ $departments->sum('programs_count') }}</h3>
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
                            <h5 class="card-title mb-1">Active Departments</h5>
                            <h3 class="mb-0">{{ $departments->where('is_active', true)->count() }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Avg Programs</h5>
                            <h3 class="mb-0">{{ $departments->count() > 0 ? round($departments->sum('programs_count') / $departments->count(), 1) : 0 }}</h3>
                        </div>
                        <i class="fas fa-chart-bar fa-2x opacity-75"></i>
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
                                            @if($department->is_active)
                                                <span class="badge bg-success badge-sm">Active</span>
                                            @else
                                                <span class="badge bg-secondary badge-sm">Inactive</span>
                                            @endif
                                        </div>
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
                                    
                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="mb-0 text-primary">{{ $department->programs_count }}</h4>
                                                <small class="text-muted">Programs</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="mb-0 text-success">{{ $department->faculty_count ?? 0 }}</h4>
                                            <small class="text-muted">Faculty</small>
                                        </div>
                                    </div>
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
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Last updated: {{ $department->updated_at->diffForHumans() }}
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
</style>
@endsection
