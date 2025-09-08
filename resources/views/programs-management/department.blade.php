@extends('layouts.app')

@section('title', 'Programs in ' . $department->name . ' - TracAdemics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $department->name }} Programs</h1>
            <p class="text-muted">Department Code: {{ $department->code }}</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('programs-management.index') }}">Programs Management</a></li>
                <li class="breadcrumb-item active">{{ $department->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Department Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">{{ $department->name }}</h4>
                            <p class="text-muted mb-1">Department Code: <span class="badge bg-secondary">{{ $department->code }}</span></p>
                            @if($department->description)
                                <p class="mb-0 text-muted">{{ $department->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-primary mb-0">{{ $department->programs->count() }}</h3>
                            <small class="text-muted">Programs</small>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success mb-0">{{ $department->faculty_count ?? 0 }}</h3>
                            <small class="text-muted">Faculty</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Programs Section -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-graduation-cap me-2"></i>
                Academic Programs
            </h5>
            <a href="{{ route('programs-management.create', $department->id) }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Program
            </a>
        </div>
        <div class="card-body">
            @forelse($department->programs as $program)
                <div class="row mb-4">
                    @foreach($department->programs->chunk(3) as $programChunk)
                        @foreach($programChunk as $program)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-start border-primary border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title text-primary mb-1">{{ $program->name }}</h6>
                                            <p class="text-muted mb-2">
                                                <small>
                                                    <i class="fas fa-tag me-1"></i>Code: {{ $program->code }}
                                                </small>
                                            </p>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View Details</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit Program</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    @if($program->description)
                                        <p class="card-text small text-muted mb-3">{{ Str::limit($program->description, 80) }}</p>
                                    @endif
                                    
                                    <div class="border-top pt-3">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Duration</small>
                                                <span class="badge bg-info">{{ $program->duration ?? 'N/A' }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Status</small>
                                                @if($program->is_active ?? true)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                </div>
                @break
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Programs Found</h5>
                    <p class="text-muted">This department doesn't have any programs yet.</p>
                    <a href="{{ route('programs-management.create', $department->id) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add First Program
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-4">
        <a href="{{ route('programs-management.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Programs Management
        </a>
    </div>
</div>

<style>
    .border-start {
        border-left-width: 4px !important;
    }
    
    .border-primary {
        border-color: #28a745 !important;
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
    
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
</style>
@endsection
