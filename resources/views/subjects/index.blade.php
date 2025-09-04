@extends('layouts.app')

@section('title', 'Subject Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-book me-2"></i>Subject Management</h2>
                <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Subject
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="row mb-4">
                <!-- Statistics Cards -->
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Subjects</h6>
                                    <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-book fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Active Subjects</h6>
                                    <h3 class="mb-0">{{ $stats['active'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Assigned</h6>
                                    <h3 class="mb-0">{{ $stats['assigned'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-user-tie fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Unassigned</h6>
                                    <h3 class="mb-0">{{ $stats['unassigned'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-user-plus fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('subjects.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search Subjects</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Subject code or name...">
                            </div>
                            <div class="col-md-3">
                                <label for="program" class="form-label">Program</label>
                                <select class="form-select" id="program" name="program">
                                    <option value="">All Programs</option>
                                    @foreach($programs ?? [] as $program)
                                    <option value="{{ $program->id }}" {{ request('program') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="year_level" class="form-label">Year Level</label>
                                <select class="form-select" id="year_level" name="year_level">
                                    <option value="">All Years</option>
                                    <option value="1" {{ request('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                                    <option value="2" {{ request('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                                    <option value="3" {{ request('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                                    <option value="4" {{ request('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                                <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Subjects List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Subjects List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Program</th>
                                    <th>Year Level</th>
                                    <th>Semester</th>
                                    <th>Units</th>
                                    <th>Assigned Faculty</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subjects as $subject)
                                <tr>
                                    <td>
                                        <strong>{{ $subject->code }}</strong>
                                    </td>
                                    <td>{{ $subject->name }}</td>
                                    <td>{{ $subject->program->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $subject->year_level ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $subject->semester ?? 'N/A' }}</td>
                                    <td>{{ $subject->units ?? 0 }}</td>
                                    <td>
                                        @if($subject->faculty)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>
                                                <div>
                                                    <small class="fw-bold">{{ $subject->faculty->name }}</small><br>
                                                    <small class="text-muted">{{ $subject->faculty->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subject->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('subjects.show', $subject) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!$subject->faculty)
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#assignModal{{ $subject->id }}" title="Assign Faculty">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Assignment Modal -->
                                @if(!$subject->faculty)
                                <div class="modal fade" id="assignModal{{ $subject->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('subjects.assign', $subject) }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Assign Faculty to {{ $subject->code }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="faculty_id{{ $subject->id }}" class="form-label">Select Faculty</label>
                                                        <select class="form-select" id="faculty_id{{ $subject->id }}" name="faculty_id" required>
                                                            <option value="">Choose faculty member...</option>
                                                            @foreach($availableFaculty ?? [] as $faculty)
                                                            <option value="{{ $faculty->id }}">{{ $faculty->name }} ({{ $faculty->email }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Assign Faculty</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-book fa-3x mb-3 d-block"></i>
                                        No subjects found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($subjects) && method_exists($subjects, 'links'))
                        <div class="mt-3">
                            {{ $subjects->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
