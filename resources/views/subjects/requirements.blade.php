@extends('layouts.app')

@section('title', 'Subject Requirements')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('subjects.assigned') }}">My Subjects</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $subject->code }}</li>
                </ol>
            </nav>
            <h2><i class="fas fa-tasks me-2"></i>{{ $subject->code }} - Requirements</h2>
            <p class="text-muted">{{ $subject->name }}</p>
        </div>
    </div>

    <!-- Subject Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ $subject->name }}</h4>
                            <p class="mb-0">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $assignment->semester->name }} {{ $assignment->semester->academic_year }}
                            </p>
                            @if($subject->description)
                            <p class="mb-0 mt-2 opacity-75">{{ $subject->description }}</p>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            <h3>{{ $stats['completion_percentage'] }}% Complete</h3>
                            <small>{{ $stats['completed_requirements'] }}/{{ $stats['total_requirements'] }} Requirements</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['completed_requirements'] }}</h4>
                    <small>Completed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['pending_requirements'] }}</h4>
                    <small>Pending Review</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['rejected_requirements'] }}</h4>
                    <small>Rejected</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['not_submitted_requirements'] }}</h4>
                    <small>Not Submitted</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Requirements Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Document Requirements</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Document Type</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Review Comments</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requirements as $requirement)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $requirement['document_type']->name }}</strong>
                                    @if($requirement['document_type']->description)
                                    <br>
                                    <small class="text-muted">{{ $requirement['document_type']->description }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($requirement['status'] === 'approved')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Approved
                                    </span>
                                @elseif($requirement['status'] === 'pending')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>Pending Review
                                    </span>
                                @elseif($requirement['status'] === 'rejected')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Rejected
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-exclamation me-1"></i>Not Submitted
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($requirement['submitted_at'])
                                    <small>{{ $requirement['submitted_at']->format('M j, Y g:i A') }}</small>
                                @else
                                    <small class="text-muted">Not submitted</small>
                                @endif
                            </td>
                            <td>
                                @if($requirement['review_comments'])
                                    <small>{{ $requirement['review_comments'] }}</small>
                                @else
                                    <small class="text-muted">No comments</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($requirement['submission'])
                                        @if($requirement['file_path'])
                                            <a href="{{ asset('storage/' . $requirement['file_path']) }}" 
                                               class="btn btn-outline-info" 
                                               target="_blank" 
                                               title="View File">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @endif
                                        
                                        @if($requirement['link_url'])
                                            <a href="{{ $requirement['link_url'] }}" 
                                               class="btn btn-outline-primary" 
                                               target="_blank" 
                                               title="Open Link">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                        
                                        @if($requirement['status'] === 'rejected' || $requirement['status'] === 'not_submitted')
                                            <a href="{{ route('compliance.create', ['subject_id' => $subject->id, 'document_type_id' => $requirement['document_type']->id]) }}" 
                                               class="btn btn-outline-success" 
                                               title="Resubmit">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('compliance.create', ['subject_id' => $subject->id, 'document_type_id' => $requirement['document_type']->id]) }}" 
                                           class="btn btn-success btn-sm" 
                                           title="Submit Document">
                                            <i class="fas fa-upload me-1"></i>Submit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
