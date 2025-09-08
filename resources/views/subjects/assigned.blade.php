@extends('layouts.app')

@section('title', 'My Assigned Subjects')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-book-open me-2"></i>My Assigned Subjects</h2>
            <p class="text-muted">Manage and submit requirements for your assigned subjects</p>
        </div>
    </div>

    <!-- Subjects Grid -->
    <div class="row">
        @forelse($subjects as $subjectData)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $subjectData['subject']->code }}</h5>
                    <span class="badge badge-completion completion-{{ $subjectData['completion_percentage'] >= 80 ? 'high' : ($subjectData['completion_percentage'] >= 60 ? 'medium' : 'low') }}">
                        {{ $subjectData['completion_percentage'] }}%
                    </span>
                </div>
                <div class="card-body">
                    <h6 class="card-title">{{ $subjectData['subject']->name }}</h6>
                    
                    @if($subjectData['subject']->description)
                    <p class="text-muted small mb-3">{{ $subjectData['subject']->description }}</p>
                    @endif
                    
                    <!-- Assignment Info -->
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $subjectData['assignment']->semester->name }} {{ $subjectData['assignment']->semester->academic_year }}
                        </small>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Completion Progress</small>
                            <small class="text-muted">{{ $subjectData['completed_requirements'] }}/{{ $subjectData['total_requirements'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $subjectData['completion_percentage'] }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Requirements Summary -->
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted">Total Requirements</small>
                            <h6 class="text-primary">{{ $subjectData['total_requirements'] }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Completed</small>
                            <h6 class="text-success">{{ $subjectData['completed_requirements'] }}</h6>
                        </div>
                    </div>
                    
                    <!-- Quick Status -->
                    <div class="mt-3">
                        @php
                            $pendingCount = $subjectData['requirements']->where('status', 'pending')->count();
                            $rejectedCount = $subjectData['requirements']->where('status', 'rejected')->count();
                            $notSubmittedCount = $subjectData['requirements']->where('status', 'not_submitted')->count();
                        @endphp
                        
                        @if($pendingCount > 0)
                            <div class="text-warning small">
                                <i class="fas fa-clock me-1"></i>{{ $pendingCount }} pending review
                            </div>
                        @endif
                        
                        @if($rejectedCount > 0)
                            <div class="text-danger small">
                                <i class="fas fa-times-circle me-1"></i>{{ $rejectedCount }} need resubmission
                            </div>
                        @endif
                        
                        @if($notSubmittedCount > 0)
                            <div class="text-muted small">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $notSubmittedCount }} not submitted
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('subjects.assigned.show', $subjectData['subject']) }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-tasks me-1"></i>View Requirements
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h5>No Subjects Assigned</h5>
                    <p class="text-muted">You don't have any subjects assigned for the current semester yet.</p>
                    <p class="text-muted small">Please contact your Program Head for subject assignments.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
.badge-completion {
    font-size: 0.8em;
    padding: 0.4em 0.8em;
}

.completion-high {
    background-color: #28a745;
    color: white;
}

.completion-medium {
    background-color: #ffc107;
    color: #212529;
}

.completion-low {
    background-color: #dc3545;
    color: white;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endsection
