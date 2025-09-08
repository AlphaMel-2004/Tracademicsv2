@extends('layouts.app')

@section('title', 'Semester Management - TracAdemics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Semester Management</h1>
            <p class="text-muted">Manage academic semesters and sessions</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">System Settings</a></li>
                <li class="breadcrumb-item active">Semesters</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Total Semesters</h5>
                            <h3 class="mb-0">{{ $semesters->count() }}</h3>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Active Semester</h5>
                            <h3 class="mb-0">{{ $activeSemester ? '1' : '0' }}</h3>
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
                            <h5 class="card-title mb-1">Current Year</h5>
                            <h3 class="mb-0">{{ date('Y') }}</h3>
                        </div>
                        <i class="fas fa-calendar fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Sessions</h5>
                            <h3 class="mb-0">{{ $activeSemester ? $activeSemester->semesterSessions->count() : '0' }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Semester Info -->
    @if($activeSemester)
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <h6 class="mb-1">Current Active Semester</h6>
                <p class="mb-0">{{ $activeSemester->name }} - {{ $activeSemester->academic_year }}</p>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
                <h6 class="mb-1">No Active Semester</h6>
                <p class="mb-0">Please activate a semester to enable the compliance system.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Add Semester Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-plus me-2"></i>
                Add New Semester
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('settings.semesters.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Semester Name <span class="text-danger">*</span></label>
                            <select name="name" id="name" class="form-control @error('name') is-invalid @enderror" required>
                                <option value="">Select Semester</option>
                                <option value="1st Semester" {{ old('name') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                                <option value="2nd Semester" {{ old('name') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                                <option value="Summer" {{ old('name') == 'Summer' ? 'selected' : '' }}>Summer</option>
                            </select>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <input type="text" name="academic_year" id="academic_year" 
                                   class="form-control @error('academic_year') is-invalid @enderror" 
                                   value="{{ old('academic_year', date('Y') . '-' . (date('Y') + 1)) }}" 
                                   placeholder="e.g., 2024-2025" required>
                            @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1">
                            <label for="is_active" class="form-check-label">
                                Set as Active Semester
                            </label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Add Semester
                </button>
            </form>
        </div>
    </div>

    <!-- Semesters Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                All Semesters
            </h5>
        </div>
        <div class="card-body p-0">
            @if($semesters->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Semester</th>
                                <th>Academic Year</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Sessions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($semesters as $semester)
                                <tr class="{{ $semester->is_active ? 'table-success' : '' }}">
                                    <td>
                                        <strong>{{ $semester->name }}</strong>
                                        @if($semester->is_active)
                                            <span class="badge bg-success ms-2">Active</span>
                                        @endif
                                    </td>
                                    <td>{{ $semester->academic_year }}</td>
                                    <td>
                                        <small>
                                            {{ $semester->start_date->format('M d, Y') }} - 
                                            {{ $semester->end_date->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($semester->start_date <= now() && $semester->end_date >= now())
                                            <span class="badge bg-success">Current</span>
                                        @elseif($semester->start_date > now())
                                            <span class="badge bg-warning">Upcoming</span>
                                        @else
                                            <span class="badge bg-secondary">Past</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $semester->semesterSessions->count() }}</span>
                                    </td>
                                    <td>
                                        @if(!$semester->is_active)
                                            <form action="{{ route('settings.semesters.activate', $semester) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" 
                                                        onclick="return confirm('Are you sure you want to activate this semester? This will deactivate the current active semester.')">
                                                    <i class="fas fa-play"></i> Activate
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-success">
                                                <i class="fas fa-check-circle"></i> Active
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Semesters Found</h5>
                    <p class="text-muted">Add your first semester using the form above.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
