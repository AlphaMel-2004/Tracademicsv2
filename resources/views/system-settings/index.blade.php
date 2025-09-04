@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-cogs me-2"></i>System Settings</h2>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="row mb-4">
                <!-- System Statistics -->
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Semesters</h6>
                                    <h3 class="mb-0">{{ $stats['total_semesters'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-calendar fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Document Types</h6>
                                    <h3 class="mb-0">{{ $stats['total_document_types'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-file-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Active Sessions</h6>
                                    <h3 class="mb-0">{{ $stats['active_sessions'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">System Users</h6>
                                    <h3 class="mb-0">{{ $stats['system_users'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-user-cog fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="{{ route('settings.semesters') }}" class="btn btn-outline-primary w-100 mb-3">
                                        <i class="fas fa-calendar-alt fa-2x d-block mb-2"></i>
                                        Manage Semesters
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('settings.document-types') }}" class="btn btn-outline-success w-100 mb-3">
                                        <i class="fas fa-file-alt fa-2x d-block mb-2"></i>
                                        Document Types
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-info w-100 mb-3">
                                        <i class="fas fa-users-cog fa-2x d-block mb-2"></i>
                                        User Management
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('departments.index') }}" class="btn btn-outline-warning w-100 mb-3">
                                        <i class="fas fa-building fa-2x d-block mb-2"></i>
                                        Departments
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Semester Info -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Current Active Semester</h5>
                        </div>
                        <div class="card-body">
                            @if($activeSemester)
                                <h4 class="text-primary">{{ $activeSemester->name }}</h4>
                                <p class="mb-1"><strong>Code:</strong> {{ $activeSemester->code }}</p>
                                <p class="mb-1"><strong>Academic Year:</strong> {{ $activeSemester->academic_year }}</p>
                                <p class="mb-1"><strong>Start Date:</strong> {{ $activeSemester->start_date->format('M d, Y') }}</p>
                                <p class="mb-1"><strong>End Date:</strong> {{ $activeSemester->end_date->format('M d, Y') }}</p>
                                <div class="mt-3">
                                    <span class="badge bg-success">Active</span>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-exclamation-triangle fa-3x mb-3 d-block"></i>
                                    <p>No active semester found. Please activate a semester.</p>
                                    <a href="{{ route('settings.semesters') }}" class="btn btn-primary">
                                        Manage Semesters
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Document Types</h5>
                        </div>
                        <div class="card-body">
                            @if($documentTypes->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($documentTypes->take(5) as $docType)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $docType->name }}</h6>
                                            <small class="text-muted">{{ $docType->submission_type }} • Due in {{ $docType->due_days }} days</small>
                                        </div>
                                        @if($docType->is_required)
                                            <span class="badge bg-danger">Required</span>
                                        @else
                                            <span class="badge bg-info">Optional</span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 text-center">
                                    <a href="{{ route('settings.document-types') }}" class="btn btn-sm btn-outline-primary">
                                        View All Document Types
                                    </a>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-file-alt fa-2x mb-2 d-block"></i>
                                    <p>No document types found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
